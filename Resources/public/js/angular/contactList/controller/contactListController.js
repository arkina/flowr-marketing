angular.module('contactList').controller("contactListController", function ($scope, $routeParams, $http, $modal, toaster) {

    $scope.accounts = [];
    $scope.accountSel = null;

    $scope.contactListContacts = [];
    $scope.viewingContacts = [];
    $scope.selectedContacts = [];

    $scope.availableContactLists = [];
    $scope.selectedContactListToSwitch = [];
    $scope.checkedContacts = [];

    $scope.createContactUrl = createContact;
    $scope.importExtUrl = importUrl;
    $scope.importSExtUrl = importSUrl;

    $scope.search = "";

    var modalInstance;

    $scope.$watch('search', function(newValue, oldValue) {
            $scope.filterContacts(newValue);
    });

    $scope.filterContacts = function (searchStr) {
        var promise = $http.get(getContacts + "?search="+searchStr);
        promise.then(
            function (response) {
                $scope.contactListContacts = response.data;
            });
    };

    $scope.load = function () {
        /* get contacts */
        var promise = $http.get(getContacts);
        promise.then(
                function (response) {
                    $scope.contactListContacts = response.data;
                });

        /* get available contact lists */
        var promiseAvailableContactLists = $http.get(getAvailablesContactLists);
        promiseAvailableContactLists.then(
            function (response) {
                $scope.availableContactLists = response.data;
            });
    };

    $scope.findContacts = function () {
        var promise = $http.get(getAccounts);
        promise.then(
            function (response) {
                $scope.accounts = response.data;
            });

        modalInstance = $modal.open({
            size: "lg",
            templateUrl: 'findContacts.html',
            scope: $scope
        });
    };

    $scope.loadFilterContacts = function(accountId){
        console.log(accountId);
        var urlServ = rootPath + "p/api/clients/contacts/account/" + accountId;
        var promise = $http.get(urlServ);
        promise.then(
            function (response) {
                $scope.viewingContacts = response.data;
            });
    }

    $scope.addToContactList = function (selectedContacts) {
        $http.post(addContactsToList, { contacts: selectedContacts}).then(function () {
            $scope.load();
            modalInstance.close();
        }, function (response) {
            modalInstance.close();
        });

    };

    $scope.closeModal = function(){
        modalInstance.close();
    }

    $scope.bulkCopy = function(contactListIdDest){

        var contactIds = [];

        angular.forEach($scope.checkedContacts, function(value, contactId){
            if(value){
                contactIds.push(contactId);
            }
        });

        $http.post(bulkCopyUrl, {"list_dest": contactListIdDest, "contacts": contactIds}).then(function () {
            toaster.pop('success', 'Se copiaron los contactos');
        }, function (response) {

        });

    }

    $scope.bulkMove = function(contactListIdDest){

        var contactIds = [];

        angular.forEach($scope.checkedContacts, function(value, contactId){
            if(value){
                contactIds.push(contactId);
            }
        });

        $http.post(bulkMoveUrl, {"list_dest": contactListIdDest, "contacts": contactIds}).then(function () {
            toaster.pop('success', 'Se movieron los contactos');
            $scope.load();
        }, function (response) {

        });

    }


});
