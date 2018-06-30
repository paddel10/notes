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

    $scope.folderSelectorOpen = false;
    $scope.filterCategory = null;
    $scope.filterFavorite = false;

    $scope.orderRecent = ['-favorite','-modified'];
    $scope.orderAlpha = ['-favorite','title'];
    $scope.filterOrder = $scope.orderRecent;

    var notesResource = Restangular.all('notes');

    // initial request for getting all notes
    notesResource.getList().then(function (notes) {
        NotesModel.addAll(notes);
    });

    $scope.create = function () {
        notesResource.post({category: $scope.filterCategory}).then(function (note) {
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
        $scope.filterOrder = category===null ? $scope.orderRecent : $scope.orderAlpha;
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

    $scope.nthIndexOf = function(str, pattern, n) {
        var i = -1;
        while (n-- && i++ < str.length) {
            i = str.indexOf(pattern, i);
            if (i < 0) {
                break;
            }
        }
        return i;
    };

    $scope.getCategories = _.memoize(function (notes, maxLevel) {
        var categories = {};
        for(var i=0; i<notes.length; i++) {
            var cat = notes[i].category;
            if(maxLevel>0) {
                var index = $scope.nthIndexOf(cat, '/', maxLevel);
                if(index>0) {
                    cat = cat.substring(0, index);
                }
            }
            if(categories[cat]===undefined) {
                categories[cat] = 1;
            } else {
                categories[cat]++;
            }
        }
        var result = [];
        for(var category in categories) {
            result.push({ name: category, count: categories[category]});
        }
        return result;
    });

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
