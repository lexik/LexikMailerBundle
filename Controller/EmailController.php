<?php

namespace Lexik\Bundle\MailerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Email controller.
 *
 * @author Laurent Heurtault <l.heurtault@lexik.fr>
 * @author Yoann Aparici <y.aparici@lexik.fr>
 */
class EmailController extends Controller
{
    /**
     * List all emails
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listAction(Request $request)
    {
        $pager = $this->get('lexik_mailer.simple_pager')->retrievePageElements(
            $this->container->getParameter('lexik_mailer.email_entity.class'),
            $request->get('page', 1)
        );

        return $this->render('@LexikMailer/Email/list.html.twig', array_merge(array(
            'emails'  => $pager->getResults(),
            'total'   => $pager->getCount(),
            'page'    => $pager->getPage(),
            'maxPage' => $pager->getMaxPage(),
            'layout'  => $this->container->getParameter('lexik_mailer.base_layout'),
            'locale'  => $this->container->getParameter('locale'),
        ), $this->getAdditionalParameters()));
    }

    /**
     * Email edition
     *
     * @param Request $request
     * @param string  $emailId
     * @param string  $lang
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, $emailId, $lang = null)
    {
        $class = $this->container->getParameter('lexik_mailer.email_entity.class');
        $email = $this->get('doctrine.orm.entity_manager')->find($class, $emailId);

        if (!$email) {
            throw $this->createNotFoundException(sprintf('No email found for id "%d"', $emailId));
        }

        $handler = $this->get('lexik_mailer.form.handler.email');
        $form = $handler->createForm($email, $lang);

        if ($handler->processForm($form, $request)) {
            return $this->redirect($this->generateUrl('lexik_mailer.email_edit', array(
                'emailId' => $email->getId(),
                'lang'    => $handler->getLocale(),
            )));
        }

        return $this->render('@LexikMailer/Email/edit.html.twig', array_merge(array(
            'form'          => $form->createView(),
            'layout'        => $this->container->getParameter('lexik_mailer.base_layout'),
            'email'         => $email,
            'lang'          => $handler->getLocale(),
            'displayLang'   => \Locale::getDisplayLanguage($handler->getLocale()),
            'routePattern'  => urldecode($this->generateUrl('lexik_mailer.email_edit', array('emailId' => $email->getId(), 'lang' => '%lang%'), true)),
        ), $this->getAdditionalParameters()));
    }

    /**
     * Delete email
     *
     * @param $emailId
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteAction($emailId)
    {
        $class = $this->container->getParameter('lexik_mailer.email_entity.class');

        $em = $this->get('doctrine.orm.entity_manager');
        $email = $em->find($class, $emailId);

        if (!$email) {
            throw $this->createNotFoundException(sprintf('No email found for id "%d"', $emailId));
        }

        $em->remove($email);
        $em->flush();

        return $this->redirect($this->generateUrl('lexik_mailer.email_list'));
    }

    /**
     * New email
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function newAction(Request $request)
    {
        $handler = $this->get('lexik_mailer.form.handler.email');
        $form = $handler->createForm();

        if ($handler->processForm($form, $request)) {
            return $this->redirect($this->generateUrl('lexik_mailer.email_list'));
        }

        return $this->render('@LexikMailer/Email/new.html.twig', array_merge(array(
            'form'      => $form->createView(),
            'layout'    => $this->container->getParameter('lexik_mailer.base_layout'),
            'lang'      => \Locale::getDisplayLanguage($handler->getLocale()),
        ), $this->getAdditionalParameters()));
    }

    /**
     * Preview an email
     *
     * @param int $emailId
     * @param     $lang
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function previewAction($emailId, $lang)
    {
        $class = $this->container->getParameter('lexik_mailer.email_entity.class');
        $email = $this->get('doctrine.orm.entity_manager')->find($class, $emailId);

        if (!$email) {
            throw $this->createNotFoundException(sprintf('No email found for id "%d"', $emailId));
        }

        $preview = $this->get('lexik_mailer.message_preview_generator');
        $preview->getTemplatesPreview($email, $lang);

        return $this->render('@LexikMailer/Email/preview.html.twig', array_merge(array(
            'content'  => $preview->get('content'),
            'subject'  => $preview->get('subject'),
            'fromName' => $preview->get('fromName'),
            'errors'   => $preview->getErrors(),
        ), $this->getAdditionalParameters()));
    }

    /**
     * Delete a translation
     *
     * @param string $translationId
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteTranslationAction($translationId)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $translation = $em->find('LexikMailerBundle:EmailTranslation', $translationId);

        if (!$translation) {
            throw $this->createNotFoundException(sprintf('No translation found for id "%d"', $translationId));
        }

        $em->remove($translation);
        $em->flush();

        return $this->redirect($this->generateUrl('lexik_mailer.email_edit', array('emailId' => $translation->getEmail()->getId())));
    }

    /**
     * Return some additional parameters to pass to the view.
     *
     * @return array
     */
    protected function getAdditionalParameters()
    {
        return array();
    }
}
