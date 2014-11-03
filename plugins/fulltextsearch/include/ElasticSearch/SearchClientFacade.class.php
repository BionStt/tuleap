<?php
/**
 * Copyright (c) Enalean, 2012. All Rights Reserved.
 *
 * This file is a part of Tuleap.
 *
 * Tuleap is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * Tuleap is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Tuleap. If not, see <http://www.gnu.org/licenses/>.
 */

require_once 'common/project/UGroupLiteralizer.class.php';
require_once 'common/project/ProjectManager.class.php';

/**
 * Allow to perform search on ElasticSearch
 */
class ElasticSearch_SearchClientFacade extends ElasticSearch_ClientFacade implements FullTextSearch_ISearchDocuments {

    /**
     * @var mixed
     */
    protected $index;

    /**
     * @var ProjectManager
     */
    protected $project_manager;

    /** @var ElasticSearch_1_2_ResultFactory */
    protected $result_factory;

    public function __construct(
        ElasticSearchClient $client,
        $index,
        ProjectManager $project_manager,
        ElasticSearch_1_2_ResultFactory $result_factory
    ) {
        parent::__construct($client);
        $this->index           = $index;
        $this->project_manager = $project_manager;
        $this->result_factory  = $result_factory;
    }

    /**
     * @see ISearchDocuments::searchDocuments
     *
     * @return ElasticSearch_SearchResultCollection
     */
    public function searchInFields(array $fields, $request, $offset) {
        $terms   = trim($request->getValidated('search_fulltext', 'string', ''));
        $results = array();
        if ($terms) {
            $query        = $this->getSearchInFieldsQuery($terms, $fields, $offset);
            $searchResult = $this->client->search($query);

            $results = $this->result_factory->getChangesetIds($searchResult);
        }

        return $results;
    }

    /**
     * @see ISearchDocuments::searchDocuments
     *
     * @return ElasticSearch_SearchResultCollection
     */
    public function searchDocuments($terms, array $facets, $offset, PFUser $user, $size) {
        $query  = $this->getSearchDocumentsQuery($terms, $facets, $offset, $user, $size);
        // For debugging purpose, uncomment the statement below to see the
        // content of the request (can be directly injected in a curl request)
//         echo "<pre>".json_encode($query)."</pre>";

        $search = $this->client->search($query);
        return new ElasticSearch_SearchResultCollection(
            $search,
            $facets,
            $this->result_factory
        );
    }

    /**
     * @return array to be used for querying ES
     */
    protected function getSearchInFieldsQuery($terms, $fields, $offset) {
        $query = array(
            'from' => (int)$offset,
            'query' => array(
                'multi_match' => array(
                    'query'  => $terms,
                    'fields' => $fields
                )
            ),
            'fields' => array(
                'id',
                'group_id',
                'last_changeset_id'
            )
        );
        return $query;
    }

    /**
     * @return array to be used for querying ES
     */
    protected function getSearchDocumentsQuery($terms, array $facets, $offset, PFUser $user, $size) {
        $returned_fields = array_merge($this->getReturnedFieldsForDocman(), $this->getReturnedFieldsForWiki());
        $returned_fields = array_merge($returned_fields, array('id', 'group_id'));

        $query = array(
            'from'  => (int)$offset,
            'size'  => $size,
            'query' => array(
                'query_string' => array(
                    'query' => $terms
                )
            ),
            'fields'    => $returned_fields,
            'highlight' => array(
                'pre_tags' => array('<em class="fts_word">'),
                'post_tags' => array('</em>'),
                'fields' => $this->getHighlightFieldsForDocman() + $this->getHighlightFieldsForWiki()
            ),
            'facets' => array(
                'projects' => array(
                    'terms' => array(
                        'field' => 'group_id'
                    )
                )
            ),
        );
        $this->filterWithGivenFacets($query, $facets);
        $this->filterQueryWithPermissions($query, $user);
        return $query;
    }

    private function getReturnedFieldsForDocman() {
        return array('title');
    }

    private function getReturnedFieldsForWiki() {
        return array('page_name');
    }

    private function getHighlightFieldsForDocman() {
        return array('file' => new stdClass());
    }

    private function getHighlightFieldsForWiki() {
        return array('content' => new stdClass());
    }

    protected function filterQueryWithPermissions(array &$query, PFUser $user) {
        $ugroup_literalizer = new UGroupLiteralizer();
        $filtered_query = array(
            'filtered' => array(
                'query'  => $query['query'],
                'filter' => array(
                    'terms' => array(
                        'permissions' => $ugroup_literalizer->getUserGroupsForUserWithArobase($user)
                    )
                )
            )
        );
        $query['query'] = $filtered_query;
    }

    private function filterWithGivenFacets(array &$query, array $facets) {
        if (isset($facets['group_id'])) {
            $query['filter'] = $this->filterOnProjectIds($facets['group_id']);
        }

        if (isset($facets[ElasticSearch_SearchResultMyProjectsFacet::IDENTIFIER])) {
            $query['filter'] = $this->filterOnProjectIds(explode(',', $facets[ElasticSearch_SearchResultMyProjectsFacet::IDENTIFIER]));
        }
    }

    private function filterOnProjectIds($group_ids) {
        $filter_on_project = array('or' => array());

        foreach ($group_ids as $group_id) {
            $filter_on_project['or'][] = array(
                'range' => array(
                    'group_id' => array(
                        'from' => $group_id,
                        'to'   => $group_id
                    )
                )
            );
        }

        return $filter_on_project;
    }
}
