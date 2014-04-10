<?php

namespace Lexik\Bundle\MailerBundle\Controller;

use Lexik\Bundle\MailerBundle\Entity\Layout;
use Lexik\Bundle\MailerBundle\Entity\LayoutTranslation;
use Lexik\Bundle\MailerBundle\Form\LayoutType;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Locale\Locale;

/**
 * Layout controller.
 *
 * @author Laurent Heurtault <l.heurtault@lexik.fr>
 */
class LayoutController extends Controller
{
    /**
     * List all layouts
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listAction()
    {
        $layouts = $this->get('doctrine.orm.entity_manager')->getRepository('LexikMailerBundle:Layout')->findAll();

        return $this->container->get('templating')->renderResponse('LexikMailerBundle:Layout:list.html.twig', array(
            'layouts'   => $layouts,
            'layout'    => $this->container->getParameter('lexik_mailer.base_layout'),
        ));
    }

    /**
     * Layout edition
     *
     * @param string $layoutId
     * @param string $lang
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editAction($layoutId, $lang = null)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $request = $this->get('request');
        $lang = $lang ? : $this->container->getParameter('locale');

        $layout= $em->find('LexikMailerBundle:Layout', $layoutId);
        $translation = $layout->getTranslation($lang);

        if (!$layout) {
            throw new NotFoundHttpException('Layout not found');
        }

        $form = $this->createForm(new LayoutType(), $layout, array(
                    'data_translation'      => $translation,
                    'edit'                  => true,
                ));

        // Submit form
        if ('POST' === $request->getMethod()) {
            $form->bind($request);
            if ($form->isValid()) {
                $em->persist($translation);
                $em->flush();

                return $this->redirect($this->generateUrl('lexik_mailer.layout_edit', array(
                            'layoutId'   => $layout->getId(),
                            'lang'      => $lang,
                        )));
            }
        }

        return $this->render('LexikMailerBundle:Layout:edit.html.twig', array(
            'form'          => $form->createView(),
            'base_layout'   => $this->container->getParameter('lexik_mailer.base_layout'),
            'layout'        => $layout,
            'lang'          => $lang,
            'displayLang'   => Locale::getDisplayLanguage($lang),
            'routePattern'  => urldecode($this->generateUrl('lexik_mailer.layout_edit', array('layoutId' => $layout->getId(), 'lang' => '%lang%'), true)),
        ));
    }

    /**
     * Delete layout
     *
     * @param $layoutId
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteAction($layoutId)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $layout = $em->find('LexikMailerBundle:Layout', $layoutId);

        if (!$layout) {
            throw new NotFoundHttpException('Layout not found');
        }

        $layout->getTranslations()->forAll(function($key, $translation) use ($em) {
            $em->remove($translation);
        });

        $em->remove($layout);
        $em->flush();

        return $this->redirect($this->generateUrl('lexik_mailer.layout_list'));
    }

    /**
     * New layout
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function newAction()
    {
        $request = $this->get('request');
        $layout = new Layout();
        $translation = new LayoutTranslation($this->container->getParameter('locale'));

        $translation->setLayout($layout);
        $form = $this->createForm(new LayoutType(), $layout, array(
                    'data_translation' => $translation,
                ));
        // Submit form
        if ('POST' === $request->getMethod()) {
            $form->bind($request);

            if ($form->isValid()) {
                $em = $this->get('doctrine.orm.entity_manager');

                $em->persist($translation);
                $em->persist($layout);
                $em->flush();

                return $this->redirect($this->generateUrl('lexik_mailer.layout_list'));
            }
        }

        return $this->render('LexikMailerBundle:Layout:new.html.twig', array(
            'form'      => $form->createView(),
            'layout'    => $this->container->getParameter('lexik_mailer.base_layout'),
            'lang'      => Locale::getDisplayLanguage($translation->getLang()),
        ));
    }

    /**
     * Delete a translation
     *
     * @param int $translationId
     * @throws NotFoundHttpException
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteTranslationAction($translationId)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $translation = $em->find('LexikMailerBundle:LayoutTranslation', $translationId);

        if (!$translation) {
            throw new NotFoundHttpException('Translation not found');
        }

        $em->remove($translation);
        $em->flush();

        return $this->redirect($this->generateUrl('lexik_mailer.layout_edit', array('layoutId' => $translation->getLayout()->getId())));
    }
}