<?php

namespace Lexik\Bundle\MailerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

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
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listAction(Request $request)
    {
        $pager = $this->get('lexik_mailer.simple_pager')->retrievePageElements(
            'LexikMailerBundle:Layout',
            $request->get('page', 1)
        );

        return $this->render('@LexikMailer/Layout/list.html.twig', array_merge(array(
            'layouts' => $pager->getResults(),
            'total'   => $pager->getCount(),
            'page'    => $pager->getPage(),
            'maxPage' => $pager->getMaxPage(),
            'layout'  => $this->container->getParameter('lexik_mailer.base_layout'),
        ), $this->getAdditionalParameters()));
    }

    /**
     * Layout edition
     *
     * @param Request $request
     * @param string $layoutId
     * @param string $lang
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, $layoutId, $lang = null)
    {
        $layout= $this->get('doctrine.orm.entity_manager')->find('LexikMailerBundle:Layout', $layoutId);

        if (!$layout) {
            throw $this->createNotFoundException(sprintf('No layout found for id "%d".', $layoutId));
        }

        $handler = $this->get('lexik_mailer.form.handler.layout');
        $form = $handler->createForm($layout, $lang);

        if ($handler->processForm($form, $request)) {
            return $this->redirect($this->generateUrl('lexik_mailer.layout_edit', array(
                'layoutId' => $layout->getId(),
                'lang'     => $lang,
            )));
        }

        return $this->render('@LexikMailer/Layout/edit.html.twig', array_merge(array(
            'form'          => $form->createView(),
            'base_layout'   => $this->container->getParameter('lexik_mailer.base_layout'),
            'layout'        => $layout,
            'lang'          => $lang,
            'displayLang'   => \Locale::getDisplayLanguage($lang),
            'routePattern'  => urldecode($this->generateUrl('lexik_mailer.layout_edit', array('layoutId' => $layout->getId(), 'lang' => '%lang%'), true)),
        ), $this->getAdditionalParameters()));
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
            throw $this->createNotFoundException(sprintf('No layout found for id "%d".', $layoutId));
        }

        $em->remove($layout);
        $em->flush();

        return $this->redirect($this->generateUrl('lexik_mailer.layout_list'));
    }

    /**
     * New layout
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function newAction(Request $request)
    {
        $handler = $this->get('lexik_mailer.form.handler.layout');
        $form = $handler->createForm();

        if ($handler->processForm($form, $request)) {
            return $this->redirect($this->generateUrl('lexik_mailer.layout_list'));
        }

        return $this->render('@LexikMailer/Layout/new.html.twig', array_merge(array(
            'form'   => $form->createView(),
            'layout' => $this->container->getParameter('lexik_mailer.base_layout'),
            'lang'   => \Locale::getDisplayLanguage($this->container->getParameter('locale')),
        ), $this->getAdditionalParameters()));
    }

    /**
     * Delete a translation
     *
     * @param int $translationId
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteTranslationAction($translationId)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $translation = $em->find('LexikMailerBundle:LayoutTranslation', $translationId);

        if (!$translation) {
            throw $this->createNotFoundException(sprintf('No translation found for id "%d"', $translationId));
        }

        $em->remove($translation);
        $em->flush();

        return $this->redirect($this->generateUrl('lexik_mailer.layout_edit', array('layoutId' => $translation->getLayout()->getId())));
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
