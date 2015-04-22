<?php

	use helpers\helpers;
	// src/Page.php

	/**
	 * @Entity @Table(name="pages")
	 **/
	class Page
	{

		/** @Id @Column(type="integer") @GeneratedValue **/
		protected $id;

		/** @Column(type="string") **/
		protected $name;

		/** @Column(type="string", unique=true) **/
		protected $slug;

		public function getId()
		{
			return $this->id;
		}

		public function getName()
		{
			return $this->name;
		}

		public function setName($name)
		{
			$this->name = $name;
		}

		public function getSlug()
		{
			return $this->slug;
		}

		public function setSlug($slug, $entityManager)
		{
			$slug = helpers\Helpers::slugify($slug);
			$unique = false;
			$count = 0;
			while ($unique == false) {
				$pageRepository = $entityManager->getRepository('Page');
				$has_slug = $pageRepository->findOneBy(array('slug' => $slug));
				if (!$has_slug) {
					$unique = true;
				} else {
					$count++;
					$slug = $slug .'-'. $count;
				}
			}

			$this->slug = $slug;
		}
	}