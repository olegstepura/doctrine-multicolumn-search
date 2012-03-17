<?php
/**
 * @author Oleg Stepura <github@oleg.stepura.com>
 * @copyright Oleg Stepura <github@oleg.stepura.com>
 * @version $Id$
 */

namespace Ost\Doctrine\MultiColumnSearch;
use Doctrine\ORM\Mapping as ORM;
use Ost\Doctrine\MultiColumnSearch\Searchable;

/**
 * User class.
 * @ORM\Entity
 * @ORM\Table(name="user")
 * @author Oleg Stepura <github@oleg.stepura.com>
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
