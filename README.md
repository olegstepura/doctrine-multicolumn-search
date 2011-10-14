MultiColumnSearch
======
Simply search multiple entities for a user query.


Description
-----------------

This is a query builder for Doctrine ORM designed to create a query for use
with [Pagerfanta][1]. The created query searches any number of columns for a
given query which is exploded into words and queried the way that all words
must be included in the result row. Sql substitute character (%) may be included.
It is written on PHP and intended to be used with Doctrine [Doctrine ORM][2].


Description, again ;)
-----------------

The search queries are not matched against one single column in database
(as usual), but against several of them. So when you type 2 words searching for
user in user database not only user name is searched, but also user email,
second name and maybe description can be inspected to match the searched words.
What columns to match is up to you and is configurable.

Lets assume you have a database table with all the users of your system.

    -----------------------------------------------------------
    | id | name        | last_name    | email                 |
    |----|-------------|--------------|-----------------------|
    | 1  |    Leonardo | DiCaprio     | strange@mailhost.com  |
    | 2  |        John | Doe          | john.doe@leonardo.ru  |
    | 3  |        Bill | Gates        | bill@gates.it         |
    | 4  |        Will | Leonardo     | will@leo.com          |
    | 5  | Leonardo123 | De Niro      | leo@deniro.de         |
    -----------------------------------------------------------

Searching for "leonardo" will produce records 1, 2, 4 and 5.
Searching for "leo com" will produce records 1, 2, 4 and 5.
Searching for "bill com" will produce records 1, 3 and 4.


Usage example
-----------------
Properties in given class are inspected with annotation reader for the ones
marked with `\Ost\Doctrine\MultiColumnSearch\Searchable` annotation.

Here is an example of entity (it can be found in tests folder):

    /**
     * User class.
     * @ORM\Entity
     * @ORM\Table(name="user")
     * @author Oleg Stepura <oleg.stepura [at] gmail.com>
     * @version 1.0
     */
    class User
    {
    	/**
    	 * @ORM\Column(name="id")
    	 * @ORM\Id
    	 * @ORM\GeneratedValue
    	 * @var int $id
    	 */
    	private $id;

    	/**
    	 * @ORM\Column(name="name", type="string", length=64)
    	 * @Searchable
    	 * @var string $name
    	 */
    	private $name = '';

    	/**
    	 * @ORM\Column(name="email", type="string", length=255)
    	 * @Searchable
    	 * @var string $email
    	 */
    	private $email = '';

    	/**
    	 * @ORM\Column(name="password", type="string", length=40)
    	 * @var string $password
    	 */
    	private $password = '';
    }

And here is usage example.

    use Pagerfanta\Pagerfanta;
    use Pagerfanta\Adapter\DoctrineORMAdapter;
    use Doctrine\Common\Annotations\Reader;

    $reader = new IndexedReader(new AnnotationReader());
    $qb = new QueryBuilder($entityManager, $reader, __NAMESPACE__ . '\\User');
    $pagerfanta = new Pagerfanta($qb->getPagerfantaAdapter('my query'));

    // use Pagerfanta the usual way
    $currentPageResults = $pagerfanta->getCurrentPageResults();

Configured with Symfony Dependency Injection even easier:

    $container
    	->get('ost.doctrine.multicolumn_search.builder_factory')
    	->createBuilder('\\User')
    	->getPagerfanta('my query');

License
-----------------

The work is provided as is for free without any support guarantee under the
[Creative Commons CC-BY-SA][3] license.

Author
-----------------

The work was made by Oleg Stepura. If you have questions feel free to contact me at
oleg[dot]stepura [-at-] gmail[dot]com

[1]: https://github.com/whiteoctober/Pagerfanta
[2]: http://www.doctrine-project.org/projects/orm
[3]: http://creativecommons.org/licenses/by-sa/3.0/
