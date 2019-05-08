<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Eleve
 *
 * @ORM\Table(name="eleve")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\EleveRepository")
 */
class Eleve
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="nom", type="string", length=255)
     */
    private $nom;

    /**
     * @var string
     *
     * @ORM\Column(name="prenom", type="string", length=255)
     */
    private $prenom;

   /**
    * @ORM\Column(name="date_de_naissance", type="string")
    * @Assert\Date()
    */
   private $dateDeNaissance;

    /**
     * @ORM\OneToMany(targetEntity="Note", mappedBy="eleve", cascade={"persist", "remove"})
     */
    private $note;


    public function __construct()
    {
        $this->note = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set nom
     *
     * @param string $nom
     *
     * @return Eleve
     */
    public function setNom($nom)
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * Get nom
     *
     * @return string
     */
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * Set prenom
     *
     * @param string $prenom
     *
     * @return Eleve
     */
    public function setPrenom($prenom)
    {
        $this->prenom = $prenom;

        return $this;
    }

    /**
     * Get prenom
     *
     * @return string
     */
    public function getPrenom()
    {
        return $this->prenom;
    }

   /**
    * Set dateDeNaissance
    *
    * @param string $dateDeNaissance
    *
    * @return Eleve
    */
   public function setDateDeNaissance($dateDeNaissance)
   {
       $this->dateDeNaissance = $dateDeNaissance;

       return $this;
   }

   /**
    * Get dateDeNaissance
    *
    * @return string
    */
   public function getDateDeNaissance()
   {
       return $this->dateDeNaissance;
   }

    /**
     * @return mixed
     */
    public function getNote()
    {
        return $this->note;
    }

    /**
     * @param mixed $note
     */
    public function setNote($note)
    {
        $this->note = $note;
    }

}

