<?php

namespace Lexik\Bundle\MailerBundle\Controller;

use Lexik\Bundle\MailerBundle\Entity\Email;
use Lexik\Bundle\MailerBundle\Entity\EmailTranslation;
use Lexik\Bundle\MailerBundle\Form\EmailType;

use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Locale\Locale;

/**
 * Email controller.
 *
 * @author Laurent Heurtault <l.heurtault@lexik.fr>
 * @author Yoann Aparici <y.aparici@lexik.fr>
 */
class EmailController extends ContainerAware
{
    /**
     * List all emails
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listAction()
    {
        $em = $this->container->get('doctrine.orm.entity_manager');
        $emails = $em->getRepository($this->container->getParameter('lexik_mailer.email_entity.class'))->findAll();

        return $this->container->get('templating')->renderResponse('LexikMailerBundle:Email:list.html.twig', array(
            'emails' => $emails,
            'layout' => $this->container->getParameter('lexik_mailer.base_layout'),
            'locale' => $this->container->getParameter('locale'),
        ));
    }

    /**
     * Email edition
     *
     * @param string $emailId
     * @param string $lang
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editAction($emailId, $lang = null)
    {
        $em = $this->container->get('doctrine.orm.entity_manager');
        $request = $this->container->get('request');
        $lang = $lang ? : $this->container->getParameter('locale');

        $email = $em->find('LexikMailerBundle:Email', $emailId);
        $translation = $email->getTranslation($lang);

        if (!$email) {
            throw new NotFoundHttpException('Email not found');
        }

        $form = $this->container->get('form.factory')->create(new EmailType(), $email, array(
            'data_translation' => $translation,
            'edit'             => true,
        ));

        // Submit form
        if ('POST' === $request->getMethod()) {
            $form->bind($request);

            if ($form->isValid()) {
                $em = $this->container->get('doctrine.orm.entity_manager');
                $em->persist($translation);
                $em->flush();

                return new RedirectResponse($this->container->get('router')->generate('lexik_mailer.email_edit', array(
                    'emailId' => $email->getId(),
                    'lang'    => $lang,
                )));
            }
        }

        return $this->container->get('templating')->renderResponse('LexikMailerBundle:Email:edit.html.twig', array(
            'form'          => $form->createView(),
            'layout'        => $this->container->getParameter('lexik_mailer.base_layout'),
            'email'         => $email,
            'lang'          => $lang,
            'displayLang'   => Locale::getDisplayLanguage($lang),
            'routePattern'  => urldecode($this->container->get('router')->generate('lexik_mailer.email_edit', array('emailId' => $email->getId(), 'lang' => '%lang%'), true)),
        ));
    }

    /**
     * Delete email
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteAction($emailId)
    {
        $em = $this->container->get('doctrine.orm.entity_manager');
        $email = $em->find('LexikMailerBundle:Email', $emailId);

        if (!$email) {
            throw new NotFoundHttpException('Email not found');
        }

        $email->getTranslations()->forAll(function($key, $translation) use ($em) {
            $em->remove($translation);
        });

        $em->remove($email);
        $em->flush();

        return new RedirectResponse($this->container->get('router')->generate('lexik_mailer.email_list'));
    }

    /**
     * New email
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function newAction()
    {
        $request = $this->container->get('request');

        $email = new Email();
        $translation = new EmailTranslation($this->container->getParameter('locale'));
        $translation->setEmail($email);

        $form = $this->container->get('form.factory')->create(new EmailType(), $email, array(
            'data_translation' => $translation,
        ));

        // Submit form
        if ('POST' === $request->getMethod()) {
            $form->bind($request);

            if ($form->isValid()) {
                $em = $this->container->get('doctrine.orm.entity_manager');
                $em->persist($translation);
                $em->persist($email);
                $em->flush();

                return new RedirectResponse($this->container->get('router')->generate('lexik_mailer.email_list'));
            }
        }

        return $this->container->get('templating')->renderResponse('LexikMailerBundle:Email:new.html.twig', array(
            'form'      => $form->createView(),
            'layout'    => $this->container->getParameter('lexik_mailer.base_layout'),
            'lang'      => Locale::getDisplayLanguage($translation->getLang()),
        ));
    }

    /**
     * Preview an email
     *
     * @param int $emailId
     */
    public function previewAction($emailId, $lang)
    {
        $em = $this->container->get('doctrine.orm.entity_manager');

        $class = $this->container->getParameter('lexik_mailer.email_entity.class');
        $email = $em->find($class, $emailId);

        if (!$email) {
            throw new NotFoundHttpException('Email not found');
        }

        $email->setLocale($lang);

        $renderer = $this->container->get('lexik_mailer.message_renderer');
        $renderer->loadTemplates($email);
        $renderer->setStrictVariables(false);

        $subject = $email->getSubject();
        $fromName = $email->getFromName($this->container->getParameter('lexik_mailer.admin_email'));
        $content = $email->getBody();

        $errors = array(
            'subject'      => null,
            'from_name'    => null,
            'html_content' => null,
        );

        $suffix = $email->getChecksum();
        foreach ($errors as $template => $error) {
            try {
                $renderer->renderTemplate(sprintf('%s_%s', $template, $suffix));
            } catch(\Twig_Error $e) {
                $errors[$template] = $e->getRawMessage();
            }
        }

        $renderer->setStrictVariables(true);

        return $this->container->get('templating')->renderResponse('LexikMailerBundle:Email:preview.html.twig', array(
            'content'  => $content,
            'subject'  => $subject,
            'fromName' => $fromName,
            'errors'   => $errors,
        ));
    }

    /**
     * Delete a translation
     *
     * @param string $translationId
     * @throws NotFoundHttpException
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteTranslationAction($translationId)
    {
        $em = $this->container->get('doctrine.orm.entity_manager');
        $translation = $em->find('LexikMailerBundle:EmailTranslation', $translationId);

        if (!$translation) {
            throw new NotFoundHttpException('Translation not found');
        }

        $em->remove($translation);
        $em->flush();

        return new RedirectResponse($this->container->get('router')->generate('lexik_mailer.email_edit', array('emailId' => $translation->getEmail()->getId())));
    }
}
