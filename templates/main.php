<?php
/**
 * Copyright (c) 2013, Bernhard Posselt <dev@bernhard-posselt.com>
 * Copyright (c) 2013, Jan-Christoph Borchardt http://jancborchardt.net
 * This file is licensed under the Affero General Public License version 3 or later.
 * See the COPYING file.
 */


script('notes', [
    'vendor/bootstrap/tooltip',
    'vendor/angular/angular',
    'vendor/angular-route/angular-route',
    'vendor/restangular/dist/restangular',
    'vendor/underscore/underscore',
    'vendor/simplemde/dist/simplemde.min',
    'public/app.min'
]);

style('notes', [
    '../js/vendor/simplemde/dist/simplemde.min',
    'vendor/bootstrap/tooltip',
    'notes'
]);

?>

<div id="app" ng-app="Notes" ng-controller="AppController"
    ng-init="init('<?= $_['lastViewedNote'] ?>','<?= $_['errorMessage'] ?>')" ng-cloak>

    <script type="text/ng-template" id="note.html">
        <?php print_unescaped($this->inc('note')); ?>
    </script>

    <div id="app-navigation" ng-controller="NotesController">
        <ul class="with-icon">
            <li class="note-search">
                <span class="nav-entry icon-search">
                    <input type="text" ng-model="search" />
                </span>
            </li>
            <!-- new note button -->
            <div id="note-add">            
                <button class="icon-add app-content-list-button ng-binding" id="new-note-button" type="button" name="button" ng-click="create()"
                oc-click-focus="{ selector: '#app-content textarea' }">
                    <?php p($l->t('New note')); ?> 
                </button>
            </div>


                <li data-id="recent" class="nav-recent app-navigation-noclose" ng-class="{ active: filterCategory==null && filterFavorite==false }">
                        <a
                            ng-click="setFilter(null, false)"
                            class="nav-icon-recent svg"
                        ><?php p($l->t('Recent')); ?></a>
		</li>
<!--
                <li data-id="favorites" class="nav-favorites app-navigation-noclose" ng-class="{ active: filterCategory==null && filterFavorite==true }">
                        <a
                            ng-click="setFilter(null, true)"
                            class="nav-icon-favorites svg"
                        ><?php p($l->t('Favorites')); ?></a>
                </li>
-->

<li class="collapsible app-navigation-noclose" ng-class="{ open: folderSelectorOpen, active: filterCategory!=null }">
                <a class="nav-icon-files svg" ng-click="toggleFolderSelector()">{{!folderSelectorOpen && filterCategory!=null ? filterCategory || '<?php p($l->t('Uncategorized')); ?>' : '<?php p($l->t('Categories')); ?>'}}</a>
<ul>
             <!-- category list -->
             <li
                  ng-repeat="category in (getCategories(notes, 1) | orderBy:['name'])"
                  class="nav-files"
                  ng-class="{ active: filterCategory==category.name && filterFavorite==false }"
                  >
                        <a
                            ng-click="setFilter(category.name, false)"
			    class="svg"
			    ng-class="{ 'nav-icon-uncategorized': !category.name, 'nav-icon-files': category.name }"
				    >{{ category.name || '<?php p($l->t('Uncategorized')); ?>' }}</a>
                       <div class="app-navigation-entry-utils">
                           <ul>
                               <li class="app-navigation-entry-utils-counter">{{category.count}}</li>
                           </ul>
                      </div>
                </li>
</ul>
</li>
<li class="app-navigation-separator"></li>

            <!-- notes list -->
	    <li ng-repeat="note in filteredNotes = (notes | filter:noteFilter | and:search | orderBy:filterOrder)"
		ng-class="{ active: note.id == route.noteId,'has-error': note.error }"
                class="note-item">
                <a href="#/notes/{{ note.id }}">
                    {{ note.title | noteTitle }}
                    <span ng-if="note.unsaved">*</span>
                </a>
                <span class="utils" ng-class="{'hidden': note.error }">
                    <button class="svg action icon-delete"
                        title="<?php p($l->t('Delete note')); ?>"
                        notes-tooltip
                        data-placement="bottom"
                        ng-click="delete(note.id)"
                    ></button>
                    <button class="svg action icon-star"
                        title="<?php p($l->t('Favorite')); ?>"
                        notes-tooltip
                        data-placement="bottom"
                        ng-click="toggleFavorite(note.id)"
                        ng-class="{'icon-starred': note.favorite}"
                    ></button>
                </span>
<!--<br>{{ note.category }}-->
            </li>
            <li ng-hide="filteredNotes.length">
                <span class="nav-entry">
                    <?php p($l->t('No notes found')); ?>
                </span>
            </li>


<!--

<li class="app-navigation-separator"></li>
<li class="app-navigation-separator"></li>
<li class="app-navigation-separator"></li>

<li class="note-item"><a href="#">Current Tasks</a></li>
<li class="note-item"><a href="#">Open Questions</a></li>
<li class="note-item"><a href="#">Project Overview</a></li>


<li class="app-navigation-separator"></li>

<li class="nav-files"><a href="#" class="nav-icon-files svg">Sub-Project A</a></li>

<li class="note-item"><a href="#">Note 1</a></li>
<li class="note-item"><a href="#">Note 2</a></li>
<li class="note-item"><a href="#">Note 3</a></li>
<li class="note-item"><a href="#">Note 4</a></li>

<li class="app-navigation-separator"></li>

<li class="nav-files"><a href="#" class="nav-icon-files svg">Sub-Project B</a></li>

<li class="note-item"><a href="#">Note 1</a></li>
<li class="note-item"><a href="#">Note 2</a></li>
<li class="note-item"><a href="#">Note 3</a></li>
<li class="note-item"><a href="#">Note 4</a></li>

<li class="app-navigation-separator"></li>

-->


        </ul>
    </div>

    <div id="app-content" ng-class="{loading: is.loading}">
        <div id="app-content-container" ng-view></div>
    </div>
</div>
