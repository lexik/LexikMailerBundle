Installation
============

Add the bunde to your `composer.json` file:

```javascript
require: {
    // ...
    "lexik/mailer-bundle": "dev-master" // or use a tag from packagist.org
    // ...
}
```

Then run a composer update:

```shell
composer.phar update
# OR
composer.phar update lexik/mailer-bundle # to only update the bundle
```

Register the bundle with your kernel:

```php
<?php
// in AppKernel::registerBundles()
$bundles = array(
    // ...
    new Lexik\Bundle\MailerBundle\LexikMailerBundle(),
    // ...
);
```

___________________

Configuration
=============

The only required option is the `admin_email` option:

```yaml
# app/config/config.yml
lexik_mailer:
    admin_email: %your_admin_email%     # Admin email used to notify email templates errors
```

Change the default layout used with email template:

```yaml
# app/config/config.yml
lexik_mailer:
    base_layout: "ProjectBundle:path:template.html.twg" # default value is "LexikMailerBundle::layout.html.twig"
```

Add DKIM signature in your email:

```yaml
# app/config/config.yml
lexik_mailer:
    signer:               "dkim"
    dkim:
        private_key_path: "%kernel.root_dir%/config/dkim.key"
        domain:           "mydomain.com"
        selector:         "myselector"
```

Add some Twig extensions to the templating service.
By default the templating service used by the mailer only load the routing extension.

```yaml
# app/config/config.yml
lexik_mailer:
    templating_extensions:
        - 'twig.extension.trans' # load the translation extension
        - ...
```

Customize some classes from the bundle:

```yaml
# app/config/config.yml
lexik_mailer:
    classes:
        email_entity:      "Lexik\Bundle\MailerBundle\Entity\Email"               # the email entity to use to represent an email template
        annotation_driver: "Lexik\Bundle\MailerBundle\Mapping\Driver\Annotation"  # annotation driver used to get the user's name and email
        message_factory:   "Lexik\Bundle\MailerBundle\Message\MessageFactory"     # message factory service class
        message_renderer:  "Lexik\Bundle\MailerBundle\Message\MessageRenderer"    # message renderer service class
        signer_factory:    "Lexik\Bundle\MailerBundle\Signer\SignerFactory"       # signer factory service class
        signer_dkim:       "Lexik\Bundle\MailerBundle\Signer\DkimSigner"          # DKMI signer service class
```

The bundle provide a GUI to edit the templates, to access these pages just load the routing file:

```yaml
LexikMailerBundle:
    resource: "@LexikMailerBundle/Resources/config/routing.xml"
    prefix:   /my-prefix
```

CRUDs urls:

* Email templates:  `/my-prefix/email/list`
* Layouts template: `/my-prefix/layout/list`

___________________

Usage
=====

Fields in which you can use Twig syntax:
----------------------------------------

Layout:

* body

Emails:

* from name
* from address
* subject
* body
* body text

How to link a layout template with an email template :
------------------------------------------------------

Suppose we have a layout template with `super-layout` as reference and with the following body:

```
<div>Header</div>
    {% block content %}{% endblock %}
<div>Footer</div>
```

Now in the email template edition page select the `super-layout` layout, and fill in the body field without specifying any extends clause:

```
<div>email body :)</div>
```

When you select a layout, we will automaticaly make the email template extend the layout template you select and place it in a block named 'content' during the Swift_Message generation.

How to work with default and fallback locales:
----------------------------------------------

Suppose we have a multilingual application we want to support without adding duplicate layouts for every locale, this is easily achieved by simply selecting a default locale in the Layout. 
If an Email template which references this layout has 'Use fallback locale' enabled, we will automagically load the default locale layout if a layout with the same locale cannot be found.

Lets say we have a layout with the reference `default` set to `english` as defaultLocale and no other language.
We also have a few `registration` email templates stored, all in various locales and all with `useFallbackLocale` set to `TRUE`.

A user registers using german locale, our application will kick off the code below"
```php
	$to = $this->request->get('email');
	$params = ['data','loaded','from','registration','form', 'sanitized','ofc'];
	$locale = $request->getLocale(); // 'de'

	$message = $container->get('lexik_mailer.message_factory')->get('registration', $to, $params, $locale);
```
If the Email template does not have 'Use fallback locale' enabled, you will get a `NoTranslationException` indicating that a message with said locale could not be generated.
If the Email template does have 'Use fallback locale' enabled, the code will try to find the same locale first, but when failing it will lookup the default locale for the layout and use that to generate and return the message.

So our example above will result into a message which uses the German Email template, with the English Layout template.



Generate a Swift_Message from a given template:
-----------------------------------------------

Once you created some templates, you can create some Swift_Message instances from a given template reference by using the `lexik_mailer.message_factory` service:

```php
<?php
$to = 'me@email.com';              // an email or a User instance if you use provided annotations.
$params = array('name' => 'dude'); // template's parameters 
$locale = 'en';                    // the language to use to generate the message.

// create a swift message from the 'super-template' reference
$message = $container->get('lexik_mailer.message_factory')->get('super-template', $to, $params, $locale);
    
// then send the email
$container->get('mailer')->send($message);
```
    
    
Use annotations or implement the `EmailDestinationInterface` to make the `lexik_mailer.message_factory` service automaticaly get the name and the email address of the recipient:

```php
<?php
// SomeBundle/Entity/User.php
//...
use Lexik\Bundle\MailerBundle\Mapping\Annotation as Mailer;

class User
{
    //...
        
    /**
     * @Mailer\Name()
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
        
    /**
     * @Mailer\Address()
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }
}
```

```php
<?php
// SomeBundle/Entity/User.php
//...
use Lexik\Bundle\MailerBundle\Model\EmailDestinationInterface;

class User implements EmailDestinationInterface
{
    //...

    /**
     * @return string
     */
    public function getRecipientName()
    {
        return $this->name;
    }
        
    /**
     * @return string
     */
    public function getRecipientEmail()
    {
        return $this->email;
    }
}
```

Override Controllers:
---------------------

If you want to pass an additional parameter for all views, you can override the controllers like describe here http://symfony.com/doc/current/cookbook/bundles/inheritance.html#overriding-controllers
and override this method :

```php
<?php
class EmailController extends BaseEmailController
{
    /**
    * Example for Sonata
    */
    protected function getAdditionalParameters()
    {
        return array('admin_pool' => $this->container->get('sonata.admin.pool'));
    }
}
//...
