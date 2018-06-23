/**
 * Copyright (c) 2013, Bernhard Posselt <dev@bernhard-posselt.com>
 * This file is licensed under the Affero General Public License version 3 or
 * later.
 * See the COPYING file.
 */

// This is available by using ng-controller="NotesController" in your HTML
app.controller('NotesController', function($routeParams, $scope, $location,
                                           Restangular, NotesModel, $window) {
    'use strict';

    $scope.route = $routeParams;
    $scope.notes = NotesModel.getAll();
    $scope.categories = {};

    $scope.folderSelectorOpen = false;
    $scope.filterCategory = null;
    $scope.filterFavorite = false;

    var notesResource = Restangular.all('notes');

    // initial request for getting all notes
    notesResource.getList().then(function (notes) {
        NotesModel.addAll(notes);
    });

    // initial request for getting all categories
    notesResource.customGET('categories').then(function (categories) {
        $scope.categories = categories;
    });

    $scope.create = function () {
        notesResource.post().then(function (note) {
            NotesModel.add(note);
            $location.path('/notes/' + note.id);
        });
    };

    $scope.delete = function (noteId) {
        var note = NotesModel.get(noteId);
        note.remove().then(function () {
            NotesModel.remove(noteId);
            $scope.$emit('$routeChangeError');
        });
    };

    $scope.toggleFavorite = function (noteId) {
        var note = NotesModel.get(noteId);
        note.customPUT({favorite: !note.favorite},
            'favorite', {}, {}).then(function (favorite) {
            note.favorite = favorite ? true : false;
        });
    };

    $scope.toggleFolderSelector = function () {
        $scope.folderSelectorOpen = !$scope.folderSelectorOpen;
    };

    $scope.setFilter = function (category, favorite) {
        $scope.filterCategory = category;
        $scope.filterFavorite = favorite;
        $scope.folderSelectorOpen = false;
    };

    $scope.noteFilter = function (note) {
        if($scope.filterFavorite && !note.favorite) {
            return false;
        }
        if($scope.filterCategory!==null) {
            return note.category===$scope.filterCategory || (note.category!==null && note.category.startsWith($scope.filterCategory+'/'));
        }
        return true;
    };

    $window.onbeforeunload = function() {
        var notes = NotesModel.getAll();
        for(var i=0; i<notes.length; i++) {
            if(notes[i].unsaved) {
                return t('notes', 'There are unsaved notes. Leaving ' +
                                  'the page will discard all changes!');
            }
        }
        return null;
    };
});
