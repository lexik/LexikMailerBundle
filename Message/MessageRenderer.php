<?php

namespace Lexik\Bundle\MailerBundle\Message;

use Lexik\Bundle\MailerBundle\Model\EmailInterface;

/**
 * Render each parts of an email.
 *
 * @author Cédric Girard <c.girard@lexik.fr>
 */
class MessageRenderer
{
    /**
     * @var \Twig_Environment
     */
    private $templating;
    
    /**
     * @var array
     */
    private $loaders = array();
    
    /**
     * Construct
     *
     * @param \Twig_Environment $templating
     * @param array $defaultOptions
     */
    public function __construct(\Twig_Environment $templating, $loaders = null)
    {
        $this->templating = $templating;

        $this->templating->enableStrictVariables();
        
        if(is_array($loaders))
        {
            $this->loaders = $loaders;
        }
    }
    
    /**
     * Adds an additional twig loader to the chain loader
     * 
     * @param \Twig_LoaderInterface $loader
     */
    public function addTwigLoader(\Twig_LoaderInterface $loader)
    {
        $chain_loader = new \Twig_Loader_Chain();
    	foreach($this->loaders as $original_loader)
    	{
    		$chain_loader->addLoader($original_loader);
    	}
    	$chain_loader->addLoader($loader);
    	
    	$this->templating->setLoader($chain_loader);
    }

    /**
     * Load all templates from the email.
     *
     * @param EmailInterface $email
     */
    public function loadTemplates(EmailInterface $email)
    {
        $layout = $email->getLayoutBody();
    	$content = empty($layout) ? $email->getBody() : '{% extends \'layout\' %} {% block content %}' . $email->getBody() . '{% endblock %}';
    	
    	$loader = new \Twig_Loader_Array(array(
			'subject'   => $email->getSubject(),
			'from_name' => $email->getFromName(),
			'layout'    => $email->getLayoutBody(),
			'content'   => $content 
    	));
        
    	$this->addTwigLoader($loader);
    }

    /**
     * Render a template previously loaded.
     *
     * @param string $view
     * @param array $parameters
     * @return string
     */
    public function renderTemplate($view, array $parameters = array())
    {
        return $this->templating->render($view, $parameters);
    }

    /**
     * Enable or not strict variables.
     *
     * @param boolean $strict
     */
    public function setStrictVariables($strict)
    {
        if ((bool) $strict) {
            $this->templating->enableStrictVariables();
        } else {
            $this->templating->disableStrictVariables();
        }
    }
}
