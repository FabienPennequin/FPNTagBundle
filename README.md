FPNTagBundle
============

This bundle adds tagging to your Symfony project, with the ability to associate
tags with any number of different entities. This bundle integrates the
[DoctrineExtensions-Taggable](https://github.com/FabienPennequin/DoctrineExtensions-Taggable)
library, which handles most of the hard work.

**Navigation**

1. [Installation](#installation)
2. [Making an entity taggable](#taggable-entity)
3. [Using Tags](#using-tags)

<a name="installation"></a>

## Installation

### Use Composer

You can use composer to add the bundle :

    ```sh
    $ php composer.phar require fpn/tag-bundle
    ```

Or you can edit your composer.json, and add :

    "require": {
        "fpn/tag-bundle":"dev-master",
    }

### Register the bundle

To start using the bundle, register it in your Kernel. This file is usually
located at `app/AppKernel`:

    public function registerBundles()
    {
        $bundles = array(
            // ...
            new FPN\TagBundle\FPNTagBundle(),
        );
    )

### Create your `Tag` and `Tagging` entities

To use this bundle, you'll need to create two new entities: `Tag` and `Tagging`.
You place these in any bundle, but each should look like this:

```php
<?php

namespace Acme\TagBundle\Entity;

use FPN\TagBundle\Entity\Tag as BaseTag;

class Tag extends BaseTag
{
}
```

```php
<?php

namespace Acme\TagBundle\Entity;

use \FPN\TagBundle\Entity\Tagging as BaseTagging;

class Tagging extends BaseTagging
{
}
```

Next, you'll need to add a little bit of mapping information. One way
to do this is to create the following two XML files and place them in
the `Resources/config/doctrine` directory of your bundle:

*src/Acme/TagBundle/Resources/config/doctrine/Tag.orm.xml*:

```xml
<?xml version="1.0" encoding="utf-8"?>
<doctrine-mapping xmlns="http://doctrine-project.org/schemas/orm/doctrine-mapping"
                  xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
                  xsi:schemaLocation="http://doctrine-project.org/schemas/orm/doctrine-mapping
                  http://doctrine-project.org/schemas/orm/doctrine-mapping.xsd">

    <entity name="Acme\TagBundle\Entity\Tag" table="acme_tag">

        <id name="id" column="id" type="integer">
            <generator strategy="AUTO" />
        </id>

        <one-to-many field="tagging" target-entity="Acme\TagBundle\Entity\Tagging" mapped-by="tag" fetch="EAGER" />

    </entity>

</doctrine-mapping>
```

*src/Acme/TagBundle/Resources/config/doctrine/Tagging.orm.xml*:

```xml
<?xml version="1.0" encoding="utf-8"?>
<doctrine-mapping xmlns="http://doctrine-project.org/schemas/orm/doctrine-mapping"
                  xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
                  xsi:schemaLocation="http://doctrine-project.org/schemas/orm/doctrine-mapping
                  http://doctrine-project.org/schemas/orm/doctrine-mapping.xsd">

    <entity name="Acme\TagBundle\Entity\Tagging" table="acme_tagging">

        <id name="id" column="id" type="integer">
            <generator strategy="AUTO" />
        </id>

        <many-to-one field="tag" target-entity="Acme\TagBundle\Entity\Tag">
            <join-columns>
                <join-column name="tag_id" referenced-column-name="id" />
            </join-columns>
        </many-to-one>

        <unique-constraints>
            <unique-constraint columns="tag_id,resource_type,resource_id" name="tagging_idx" />
        </unique-constraints>

    </entity>

</doctrine-mapping>
```

You can also use Annotations :

*src/Acme/TagBundle/Entity/Tag.php*:

```php
namespace Acme\TagBundle\Entity;

use \FPN\TagBundle\Entity\Tag as BaseTag;
use Doctrine\ORM\Mapping as ORM;

/**
 * Acme\TagBundle\Entity\Tag
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Tag extends BaseTag
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\OneToMany(targetEntity="Tagging", mappedBy="tag", fetch="EAGER")
     **/
    protected $tagging;
}
```

*src/Acme/TagBundle/Entity/Tagging.php*:

```php
namespace Acme\TagBundle\Entity;

use \FPN\TagBundle\Entity\Tagging as BaseTagging;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\UniqueConstraint;

/**
 * Acme\TagBundle\Entity\Tagging
 *
 * @ORM\Table(uniqueConstraints={@UniqueConstraint(name="tagging_idx", columns={"tag_id", "resource_type", "resource_id"})})
 * @ORM\Entity
 */
class Tagging extends BaseTagging
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Tag")
     * @ORM\JoinColumn(name="tag_id", referencedColumnName="id")
     **/
    protected $tag;
}
```

<a name="taggable-entity"></a>

## Define classes on configuration

On your configuration you have to define tag and tagging classes.

Example on yaml:

```yaml

fpn_tag:
    model:
        tag_class:     Acme\TagBundle\Entity\Tag
        tagging_class: Acme\TagBundle\Entity\Tagging

```


## Making an Entity Taggable

Suppose we have a `Post` entity, and we want to make it "taggable". The setup
is simple: just add the `Taggable` interface and add the necessary 3 methods:

```php
<?php

namespace Acme\BlogBundle\Entity;

use DoctrineExtensions\Taggable\Taggable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="acme_post")
 */
class Post implements Taggable
{
    private $tags;
    
    public function getTags()
    {
        $this->tags = $this->tags ?: new ArrayCollection();

        return $this->tags;
    }

    public function getTaggableType()
    {
        return 'acme_tag';
    }

    public function getTaggableId()
    {
        return $this->getId();
    }
}
```

That's it! As you'll see in the next section, the tag manager can now manage
the tags that are associated with your entity.

<a name="using-tags"></a>

## Using Tags

The bundle works by using a "tag manager", which is responsible for creating
tags and adding them to your entities. For some really good usage instructions,
see [Using TagManager](https://github.com/FabienPennequin/DoctrineExtensions-Taggable).

Basically, the idea is this. Instead of setting tags directly on your entity
(e.g. Post), you'll use the tag manager to set the tags for you. Let's see
how this looks from inside a controller. The tag manager is available as
the `fpn_tag.tag_manager` service:

    use Acme\BlogBundle\Entity\Post;

    public function createTagsAction()
    {
        // create your entity
        $post = new Post();
        $post->setTitle('foo');

        $tagManager = $this->get('fpn_tag.tag_manager');

        // ask the tag manager to create a Tag object
        $fooTag = $tagManager->loadOrCreateTag('foo');

        // assign the foo tag to the post
        $tagManager->addTag($fooTag, $post);

        $em = $this->getDoctrine()->getEntityManager();

        // persist and flush the new post
        $em->persist($post);
        $em->flush();

        // after flushing the post, tell the tag manager to actually save the tags
        $tagManager->saveTagging($post);

        // ...

        // Load tagging ...
        $tagManager->loadTagging($post);
    }
