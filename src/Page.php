<?php

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

		/** @Column(type="text") **/
		protected $content;

		/** @Column(type="text") **/
		protected $excerpt;

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

		public function setSlug($slug)
		{
			global $entityManager;
			$slug = functions\Helpers::slugify($slug);
			$unique = false;
			$count = 0;
			while ($unique == false) {
				$pageRepository = $entityManager->getRepository('Page');
				$has_slug = $pageRepository->findOneBy(array('slug' => $slug));
				if (!$has_slug || ($has_slug->id == $this->id)) {
					$unique = true;
				} else {
					$count++;
					$slug = preg_replace('/(-\d$)/', '', $slug);
					$slug = $slug .'-'. $count;
				}
			}
			$this->slug = $slug;
		}

		public function getContent() {
			return $this->content;
		}


		public function setContent($content) {
			$this->content = $content;
		}

		public function getExcerpt() {
			return $this->excerpt;
		}

		public function setExcerpt($excerpt) {
			$this->excerpt = $excerpt;
		}

	}