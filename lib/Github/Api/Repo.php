<?php

namespace Github\Api;

/**
 * Searching repositories, getting repository information
 * and managing repository information for authenticated users.
 *
 * @link      http://develop.github.com/p/repos.html
 * @author    Thibault Duplessis <thibault.duplessis at gmail dot com>
 * @license   MIT License
 */
class Repo extends Api
{
    /**
     * Search repos by keyword
     * http://develop.github.com/p/repo.html
     *
     * @param   string  $query            the search query
     * @param   string  $language         takes the same values as the language drop down on http://github.com/search
     * @param   int     $startPage        the page number
     * @return  array                     list of repos found
     */
    public function search($query, $language = '', $startPage = 1)
    {
        //todo old api
        $response = $this->get('repos/search/'.urlencode($query), array(
            'language' => strtolower($language),
            'start_page' => $startPage
        ));

        return $response['repositories'];
    }

    /**
     * Get the repositories of a user
     * http://develop.github.com/p/repo.html
     *
     * @param   string  $username         the username
     * @return  array                     list of the user repos
     */
    public function getUserRepos($username)
    {
        return $this->get('users/'.urlencode($username).'/repos');
    }

    /**
     * Get extended information about a repository by its username and repo name
     *
     * @param   string  $username         the user who owns the repo
     * @param   string  $repo             the name of the repo
     * @return  array                     informations about the repo
     */
    public function show($username, $repo)
    {
        return $this->get('repos/'.urlencode($username).'/'.urlencode($repo));
    }

    /**
     * create repo
     *
     * @param   string  $name             name of the repository
     * @param   string  $description      repo description
     * @param   string  $homepage         homepage url
     * @param   bool    $public           1 for public, 0 for private
     * @return  array                     returns repo data
     */
    public function create($name, $description = '', $homepage = '', $public = true)
    {
        return $this->post('user/repos', array(
            'name' => $name,
            'description' => $description,
            'homepage' => $homepage,
            'private' => !$public
        ));
    }

    /**
     * delete repo
     *
     * @param   string  $name             name of the repository
     * @param   string  $token            delete token
     * @param   string  $force            force repository deletion
     *
     * @return  string|array              returns delete_token or repo status
     */
    public function delete($name, $token = null, $force = false)
    {
        //todo old api
        if ($token === null) {
            $response = $this->post('repos/delete/'.urlencode($name));

            $token = $response['delete_token'];

            if (!$force) {
                return $token;
            }
        }

        $response = $this->post('repos/delete/'.urlencode($name), array(
            'delete_token' => $token,
        ));

        return $response;
    }

    /**
     * Set information of a repository
     *
     * @param   string  $username         the user who owns the repo
     * @param   string  $repo             the name of the repo
     * @param   array   $values           the key => value pairs to post
     * @return  array                     informations about the repo
     */
    public function setRepoInfo($username, $repo, $values)
    {
        return $this->patch('repos/'.urlencode($username).'/'.urlencode($repo), $values);
    }

    /**
     * Set the visibility of a repostory to public
     *
     * @param   string  $username         the user who owns the repo
     * @param   string  $repo             the name of the repo
     * @return  array                     informations about the repo
     */
    public function setPublic($username, $repo)
    {
        $this->setRepoInfo($username, $repo, array('private' => false));
    }

    /**
     * Set the visibility of a repostory to private
     *
     * @param   string  $username         the user who owns the repo
     * @param   string  $repo             the name of the repo
     * @return  array                     informations about the repo
     */
    public function setPrivate($username, $repo)
    {
        $this->setRepoInfo($username, $repo, array('private' => true));
    }

    /**
     * Get the list of deploy keys for a repository
     *
     * @param   string  $username         the user who owns the repo
     * @param   string  $repo             the name of the repo
     * @return  array                     the list of deploy keys
     */
    public function getDeployKeys($username, $repo)
    {
        return $this->get('repos/'.urlencode($username).'/'.urlencode($repo).'/keys');
    }

    /**
     * Add a deploy key for a repository
     *
     * @param   string  $username         the user who owns the repo
     * @param   string  $repo             the name of the repo
     * @param   string  $title            the title of the key
     * @param   string  $key              the public key data
     * @return  array                     the list of deploy keys
     */
    public function addDeployKey($username, $repo, $title, $key)
    {
        return $this->post('repos/'.urlencode($username).'/'.urlencode($repo).'/keys', array(
            'title' => $title,
            'key' => $key
        ));
    }

    /**
     * Delete a deploy key from a repository
     *
     * @param   string  $username         the user who owns the repo
     * @param   string  $repo             the name of the repo
     * @param   string  $id               the the id of the key to remove
     * @return  array                     the list of deploy keys
     */
    public function removeDeployKey($username, $repo, $id)
    {
        return $this->delete('repos/'.urlencode($username).'/'.urlencode($repo).'/keys/'.urlencode($id));
    }

    /**
     * Get the collaborators of a repository
     *
     * @param   string  $username         the user who owns the repo
     * @param   string  $repo             the name of the repo
     * @return  array                     list of the repo collaborators
     */
    public function getRepoCollaborators($username, $repo)
    {
        $response = $this->get('repos/show/'.urlencode($username).'/'.urlencode($repo).'/collaborators');

        return $response['collaborators'];
    }

    /**
     * Add a collaborator to a repository
     * http://develop.github.com/p/repo.html
     *
     * @param   string  $repo             the name of the repo
     * @param   string  $username         the user who should be added as a collaborator
     * @return  array                     list of the repo collaborators
     */
    public function addRepoCollaborator($repo, $username)
    {
        $response = $this->post('repos/collaborators/'.urlencode($repo).'/add/'.urlencode($username));

        return $response['collaborators'];
    }

    /**
     * Delete a collaborator from a repository
     * http://develop.github.com/p/repo.html
     *
     * @param   string  $repo             the name of the repo
     * @param   string  $username         the user who should be removed as a collaborator
     * @return  array                     list of the repo collaborators
     */
    public function removeRepoCollaborator($repo, $username)
    {
        $response = $this->post('repos/collaborators/'.urlencode($repo).'/remove/'.urlencode($username));

        return $response['collaborators'];
    }

    /**
     * Make the authenticated user watch a repository
     * http://develop.github.com/p/repo.html
     *
     * @param   string  $username         the user who owns the repo
     * @param   string  $repo             the name of the repo
     * @return  array                     informations about the repo
     */
    public function watch($username, $repo)
    {
        $response = $this->get('repos/watch/'.urlencode($username).'/'.urlencode($repo));

        return $response['repository'];
    }

    /**
     * Make the authenticated user unwatch a repository
     * http://develop.github.com/p/repo.html
     *
     * @param   string  $username         the user who owns the repo
     * @param   string  $repo             the name of the repo
     * @return  array                     informations about the repo
     */
    public function unwatch($username, $repo)
    {
        $response = $this->get('repos/unwatch/'.urlencode($username).'/'.urlencode($repo));

        return $response['repository'];
    }

    /**
     * Make the authenticated user fork a repository
     * http://develop.github.com/p/repo.html
     *
     * @param   string  $username         the user who owns the repo
     * @param   string  $repo             the name of the repo
     * @return  array                     informations about the newly forked repo
     */
    public function fork($username, $repo)
    {
        $response = $this->get('repos/fork/'.urlencode($username).'/'.urlencode($repo));

        return $response['repository'];
    }

    /**
     * Get the tags of a repository
     * http://develop.github.com/p/repo.html
     *
     * @param   string  $username         the user who owns the repo
     * @param   string  $repo             the name of the repo
     * @return  array                     list of the repo tags
     */
    public function getRepoTags($username, $repo)
    {
        $response = $this->get('repos/show/'.urlencode($username).'/'.urlencode($repo).'/tags');

        return $response['tags'];
    }

    /**
     * Get the branches of a repository
     * http://develop.github.com/p/repo.html
     *
     * @param   string  $username         the username
     * @param   string  $repo             the name of the repo
     * @return  array                     list of the repo branches
     */
    public function getRepoBranches($username, $repo)
    {
        $response = $this->get('repos/show/'.urlencode($username).'/'.urlencode($repo).'/branches');

        return $response['branches'];
    }

    /**
     * Get the watchers of a repository
     * http://develop.github.com/p/repo.html
     *
     * @param   string  $username         the user who owns the repo
     * @param   string  $repo             the name of the repo
     * @return  array                     list of the repo watchers
     */
    public function getRepoWatchers($username, $repo)
    {
        $response = $this->get('repos/show/'.urlencode($username).'/'.urlencode($repo).'/watchers');

        return $response['watchers'];
    }

    /**
     * Get the network (a list of forks) of a repository
     * http://develop.github.com/p/repo.html
     *
     * @param   string  $username         the user who owns the repo
     * @param   string  $repo             the name of the repo
     * @return  array                     list of the repo forks
     */
    public function getRepoNetwork($username, $repo)
    {
        $response = $this->get('repos/show/'.urlencode($username).'/'.urlencode($repo).'/network');

        return $response['network'];
    }

    /**
     * Get the language breakdown of a repository
     * http://develop.github.com/p/repo.html
     *
     * @param   string  $username         the user who owns the repo
     * @param   string  $repo             the name of the repo
     * @return  array                     list of the languages
     */
    public function getRepoLanguages($username, $repo)
    {
        $response = $this->get('repos/show/'.urlencode($username).'/'.urlencode($repo).'/languages');

        return $response['languages'];
    }

    /**
     * Get the contributors of a repository
     * http://develop.github.com/p/repo.html
     *
     * @param   string  $username         the user who owns the repo
     * @param   string  $repo             the name of the repo
     * @param   boolean $includingNonGithubUsers by default, the list only shows GitHub users. You can include non-users too by setting this to true
     * @return  array                     list of the repo contributors
     */
    public function getRepoContributors($username, $repo, $includingNonGithubUsers = false)
    {
        $url = 'repos/'.urlencode($username).'/'.urlencode($repo).'/contributors';
        if ($includingNonGithubUsers) {
            $url .= '?anon=1';
        }
        $response = $this->get($url);

        return $response;
    }

}
