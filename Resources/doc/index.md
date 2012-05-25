Installation
============

Update your `deps` and `deps.lock` files:

```
// deps
...
[LexikMailerBundle]
    git=https://github.com/lexik/LexikMailerBundle.git
    target=/bundles/Lexik/Bundle/MailerBundle

// deps.lock
...
LexikMailerBundle <commit>
```

Register the namespaces with the autoloader:

```php
<?php
// app/autoload.php
$loader->registerNamespaces(array(
    // ...
    'Lexik' => __DIR__.'/../vendor/bundles',
    // ...
));
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

This is the full configuration tree with default values, only the `admin_email` option is required:

```yaml
# app/config/config.yml
lexik_mailer:
    admin_email: %your_admin_email%                     # 
    base_layout: "LexikMailerBundle::layout.html.twig"  # 
    classes:
        email_entity:      "Lexik\Bundle\MailerBundle\Entity\Email"               # the email entity to use to represent an email template
        annotation_driver: "Lexik\Bundle\MailerBundle\Mapping\Driver\Annotation"  # annotation driver used to get the user's name and email
        message_factory:   "Lexik\Bundle\MailerBundle\Message\MessageFactory"     # message factory service class
        message_renderer:  "Lexik\Bundle\MailerBundle\Message\MessageRenderer"    # message renderer service class
```

The bundle provide a GUI to edit the templates, to access these pages just load the routing file:

```yaml
LexikMailerBundle:
    resource: "@LexikMailerBundle/Resources/config/routing.xml"
    prefix:   /my-prefix
```

CRUDs urls:

* Email templates:  /my-prefix/email/list
* Layouts template: /my-prefix/layout/list

___________________

Usage
=====

Fields in which you can use Twig syntax:
----------------------------------------

Layout:

* body

Emails:

* from name
* subject
* body

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
    
    
Use annotations to make the `lexik_mailer.message_factory` service automaticaly get the name and the email address of the recipient:

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
