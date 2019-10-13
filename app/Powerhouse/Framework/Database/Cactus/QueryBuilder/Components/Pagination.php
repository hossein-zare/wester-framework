<?php

    namespace Cactus\QueryBuilder\Components;

    use PDO;
    use Cactus\QueryBuilder\Join;
    use Powerhouse\Support\HtmlString;
    use JsonSerializable;
    use ArrayAccess;

    abstract class Pagination extends Join implements JsonSerializable, ArrayAccess
    {

        /**
         * The number of results per page.
         * 
         * @var int
         */
        private $resultsPerPage = 10;

        /**
         * The number of rows.
         * 
         * @var int
         */
        private $rowCount;

        /**
         * Total pages.
         * 
         * @var int
         */
        private $totalPages;

        /**
         * Total results
         * 
         * @var int
         */
        private $totalResults;

        /**
         * The current page number.
         * 
         * @var int
         */
        private $pageNumber;

        /**
         * Pagination template.
         * 
         * @var array
         */
        private $template = [
            "ul" => "<ul class=\"pagination\">%s</ul>",
            "first" => "<li class=\"page-item\"><a href=\"%s\" class=\"page-link\">%s</a></li>",
            "prev" => "<li class=\"page-item %s\"><a href=\"%s\" class=\"page-link\">%s</a><li>",
            "pages" => "<li class=\"page-item %s\"><a href=\"%s\" class=\"page-link\">%s</a></li>",
            "next" => "<li class=\"page-item %s\"><a href=\"%s\" class=\"page-link\">%s</a><li>",
            "last" => "<li class=\"page-item\"><a href=\"%s\" class=\"page-link\">%s</a><li>",
            "more" => "<li class=\"page-item\"><a href=\"#\" class=\"page-link\">...</a><li>"
        ];

        /**
         * Get the current page number.
         * 
         * @return int
         */
        protected function pageNumber()
        {
            $page = request()->get('page');
            $page = $page !== null ? (int) $page : 1;
            $page = $page > 0 ? $page : 1;

            return $page;
        }

        /**
         * Get the page offset.
         * 
         * @param  int  $resultsPerPage
         * @param  int  $page
         * @return int
         */
        protected function pageOffset(int $resultsPerPage, int $pageNumber)
        {
            return ($pageNumber - 1) * $resultsPerPage;
        }

        /**
         * Get the total pages.
         * 
         * @param  int  $resultsPerPage
         * @param  int  $rowCount
         * @return float
         */
        protected function totalPages(int $resultsPerPage, int $rowCount)
        {
            return (int) ceil($rowCount / $resultsPerPage);
        }

        /**
         * Paginate results.
         * 
         * @param  int  $perPage
         * @return mixed
         */
        public function paginate(int $resultsPerPage)
        {
            $pageNumber = $this->pageNumber();
            $offset = $this->pageOffset($resultsPerPage, $pageNumber);

            // Get total results
            $select = $this->translate()->prepareSelect();
            $statement = $select['statement'];
            $parameters = $select['parameters'];

            $stmt = $this->conn()->prepare($statement);
            $stmt->execute($parameters);
            $this->totalResults = $stmt->rowCount();

            // Prepare the statement
            $this->limit($resultsPerPage);
            $this->offset($offset);
            $select = $this->translate()->prepareSelect();

            $statement = $select['statement'];
            $parameters = $select['parameters'];

            $stmt = $this->conn()->prepare($statement);
            $stmt->execute($parameters);

            $rowCount = $stmt->rowCount();

            if ($this->first) {
                $results = $stmt->fetch(PDO::FETCH_ASSOC);
            } else {
                $results = $stmt->fetchAll(PDO::FETCH_OBJ);
            }

            $this->items = $results;
            $this->resultsPerPage = $resultsPerPage;
            $this->rowCount = $rowCount;
            $this->pageNumber = $pageNumber;
            $this->totalPages = $this->totalPages($resultsPerPage, $this->totalResults);

            return $this;
        }

        /**
         * Get the links.
         * 
         * @param  bool  $simple
         * @return string
         */
        public function links($simple = false)
        {
            $pageNumber = $this->pageNumber;
            $totalPages = $this->totalPages;

            $html = [];
            $html[] = sprintf($this->template['prev'], ($pageNumber <= 1 ? 'disabled' : ''), ($pageNumber <= 1 ? '#' : localQuery('page=' . ($pageNumber - 1))), __('pagination.previous'));

            // Manual pages
            if ($pageNumber === 5 || $pageNumber < 5)
                $i = 1;
            elseif ($pageNumber > 5) {
                $i = ($pageNumber - 5);
                $i = $i === 1 ? 2 : $i;
            }

            if ($simple === false) {
                $breakAt = $pageNumber + 5;
                while ($i <= $breakAt && $i <= $totalPages) {
                    $html[] = sprintf($this->template['pages'], ($pageNumber === $i ? 'disabled' : ''), ($pageNumber === $i ? '#' : localQuery('page=' . $i)), $i);
                    $i++;
                }

                // Add three dots if there are more pages
                $shownPages = $i > 1 ? $i - 1 : 1;
                if ($shownPages < $totalPages) {
                    $html[] = $this->template['more'];
                }
            }

            $html[] = sprintf($this->template['next'], ($pageNumber >= $totalPages ? 'disabled' : ''), ($pageNumber >= $totalPages ? '#' : localQuery('page=' . ($pageNumber + 1))), __('pagination.next'));

            $li = "\n".implode("\n", $html)."\n";
            $links = sprintf($this->template['ul'], $li);

            return new HtmlString($links);
        }

        /**
         * Serialize json.
         * 
         * @return array
         */
        public function jsonSerialize()
        {

            return [
                'total' => $this->totalResults,
                'current_page_results' => $this->rowCount,
                'end' => ($this->pageNumber === $this->totalPages || $this->pageNumber > $this->totalPages) ? 1 : 0,
                'per_page' => $this->resultsPerPage,
                'current_page' => $this->pageNumber,
                'last_page' => $this->totalPages,
                'first_page_url' => remoteQuery(url()->full(), 'page=1'),
                'last_page_url' => remoteQuery(url()->full(), 'page=' . $this->totalPages),
                'next_page_url' => $this->pageNumber >= $this->totalPages ? null : remoteQuery(url()->full(), 'page=' . ($this->pageNumber + 1)),
                'prev_page_url' => $this->pageNumber <= 1 ? null : remoteQuery(url()->full(), 'page=' . ($this->pageNumber - 1)),
                'data' => $this->items
            ];
        }

        /**
         * {@inheritdoc}
         */
        public function offsetSet($offset, $value) {
            if (is_null($offset)) {
                $this->items[] = $value;
            } else {
                $this->items[$offset] = $value;
            }
        }

        /**
         * {@inheritdoc}
         */
        public function offsetExists($offset) {
            return isset($this->items[$offset]);
        }

        /**
         * {@inheritdoc}
         */
        public function offsetUnset($offset) {
            unset($this->items[$offset]);
        }

        /**
         * {@inheritdoc}
         */
        public function offsetGet($offset) {
            return isset($this->items[$offset]) ? $this->items[$offset] : null;
        }

    }
