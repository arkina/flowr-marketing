angular.module('templateBuilder').controller("templateBuilderController", function ($scope, $routeParams, $http, $modal, toaster) {

    $scope.template = null;

    $scope.bgcolor = null;
    $scope.bordercolor = null;
    $scope.borderwidth = null;

    $scope.loading = true;
    $scope.canDrag = false;
    $scope.nextId = 0;
    $scope.load = function () {
        var promise = $http.get(rootPath + 'mailtemplate/' + templateId + '/editor_get');
        promise.then(
                function (response) {
                    if(response.data.content === null){
                        response.data.content = "<div id='template-background' style='display: inline-block; width: 100%;'> <div style='margin: auto;width: 580px;' id='template-content' class='wrapper-fw'></div></div>";
                    }else{
                        var hasWrapper =  response.data.content.search("id=\"template-content\"");
                        if(hasWrapper < 0){
                            response.data.content = "<div id='template-background'><div style='margin: auto;width: 580px;  display: table;' id='template-content' class='wrapper-fw'>"+response.data.content+"</div></div>";
                        }
                    }

                    $scope.template = response.data;

                    $scope.loading = false;
                    $scope.$apply();
                    $scope.lastId = $(".placeholder, .editable").length;
                    CKEDITOR.inlineAll();
                    var oldId = $(".editable").length;
                    $.each($(".editable"),function(v,element){
                        var id = parseInt(element.id.replace("editor",""));
                        if(oldId < id){
                            oldId = id;
                        }
                    });
                    $scope.nextId = oldId;
                    $(".placeholder, .editable").click(function () {
                        $scope.clickonElment(this);
                    });
                    $(".placeholder, .editable").removeClass("active");
                    jscolor.init();
                    $(".border-slider").ionRangeSlider({
                        min: 0,
                        max: 100
                    });
                });

    };
    $scope.changeBGColor = function () {
        $(".canvan .active").css('background-color', "#" + $scope.bgcolor);
    };
    $scope.changeBorderColorContentTop = function (){
        $(".canvan .active").css('border-top', $scope.borderSizeTop + "px solid #" + $scope.borderColorTop );
    };
    $scope.changeBorderColorContentBottom = function (){
        $(".canvan .active").css('border-bottom', $scope.borderSizeBottom + "px solid #" + $scope.borderColorBottom );
    };
    $scope.changeBGColorBackground = function (){
        $("#template-background").css('background-color',"#"+$scope.bgcolorBackground);
    };
    $scope.changeBGColorContent = function (){
        $("#template-content").css('background-color',"#"+$scope.bgcolorContent);
    };
    $scope.changeBorderColorContent = function (){
        $("#template-content").css('border', $scope.borderSizeContent + "px solid #" + $scope.borderColorContent );
    };
    $scope.$watch('borderSizeContent', function(newValue, oldValue, scope) {
        $scope.changeBorderColorContent();
    });
    $scope.$watch('borderColorContent', function(newValue, oldValue, scope) {
        $scope.changeBorderColorContent();
    });
    $scope.$watch('bgcolorContent', function(newValue, oldValue, scope) {
        $scope.changeBGColorContent();
    });
    $scope.$watch('bgcolorBackground', function(newValue, oldValue, scope) {
        $scope.changeBGColorBackground();
    });
    $scope.$watch('borderSizeBottom', function(newValue, oldValue, scope) {
        $scope.changeBorderColorContentBottom();
    });
    $scope.$watch('borderColorBottom', function(newValue, oldValue, scope) {
        $scope.changeBorderColorContentBottom();
    });
    $scope.$watch('borderColorTop', function(newValue, oldValue, scope) {
        $scope.changeBorderColorContentTop();
    });
    $scope.$watch('borderSizeTop', function(newValue, oldValue, scope) {
        $scope.changeBorderColorContentTop();
    });
    $scope.$watch('bgcolor', function(newValue, oldValue, scope) {
        $scope.changeBGColor();
    });
    $scope.replicateElement = function () {
        var baseElement = $(".active.replicable");
        var clonedElement = baseElement.clone();
        $(".placeholder, .editable").removeClass("active");
        $(clonedElement).addClass("active");
        baseElement.parent().append(clonedElement);
        $.each(clonedElement.find(".editable"), function (key, value) {
            $(value).attr("id", $(value).attr("id") + "_" + $scope.lastId);
            $scope.lastId++;
            $scope.attachEvents($(value).attr("id"));
        });
    };
    $scope.replicable = false;
    $scope.clickonElment = function (that) {
        $(".placeholder, .editable").removeClass("active");
        $(that).addClass("active");
        $scope.replicable = $(that).hasClass("replicable");
        $scope.$apply();
    };
    $scope.attachEvents = function (id) {
        $("#" + id).click(function () {
            $scope.clickonElment(this);
        });
        if ($("#" + id).attr("default_content_6") || $("#" + id).attr("default_content")) {
            CKEDITOR.inline(id);
        }
    };
    $scope.onDropComplete = function (index, obj, evt) {
        var otherObj = $scope.items[index];
        var otherIndex = $scope.items.indexOf(obj);
        $scope.items[index] = obj;
        $scope.items[otherIndex] = otherObj;
    };

    $scope.addNewItem = function (itemType) {
        
        var itemHtml = null;
        var id = "";
        $scope.nextId++;
        switch (itemType) {
            case 'default_content':
                id = "editor" + $scope.nextId;
                itemHtml = "<div id='" + id + "' contenteditable='true' default_content='true' class='editable'  style='width: 100%;float:left;  -moz-box-sizing: border-box;-webkit-box-sizing: border-box;box-sizing: border-box;-ms-box-sizing: border-box;-o-box-sizing: border-box'>Click to edit</div>";
                $(".placeholder.active").append(itemHtml);
                break;
            case 'default_content_6':
                id = "editor" + $scope.nextId;
                itemHtml = "<div id='" + id + "' contenteditable='true' default_content_6='true' class='editable' style='width: 50%;float:left; padding-right:15px; padding-left:15px;-moz-box-sizing: border-box;-webkit-box-sizing: border-box;box-sizing: border-box;-ms-box-sizing: border-box;-o-box-sizing: border-box'>Click to edit</div>";
                $(".placeholder.active").append(itemHtml);
                break;
            case 'placeholder_row':
                id = "placeholder_" + $scope.nextId;
                itemHtml = "<div style='display:table;width:100%;'>";
                itemHtml += "<div class='placeholder' placeholder_row='true' id ='" + id + "' style='width: 100%;float:left; display: table;  -moz-box-sizing: border-box;-webkit-box-sizing: border-box;box-sizing: border-box;-ms-box-sizing: border-box;-o-box-sizing: border-box'></div>";
                itemHtml += "</div>";
                $("#template-content").append(itemHtml);
                break;
            case 'placeholder_6':
                id = "placeholder_" + $scope.nextId;
                itemHtml = "<div class='placeholder' placeholder_6='true' id ='" + id + "'  style='width: 50%;float:left;  -moz-box-sizing: border-box;-webkit-box-sizing: border-box;box-sizing: border-box;-ms-box-sizing: border-box;-o-box-sizing: border-box'></div>";
                $("#template-content").append(itemHtml);
                break;
        }
        if (id) {
            $scope.attachEvents(id);
        }
    };

    $scope.switchDragMode = function () {
        $scope.canDrag = !$scope.canDrag;
    };

    $scope.removeElement = function () {
        $(".placeholder.active, .editable.active").remove();
    };

    $scope.save = function () {

        $("#template-editor").find(".editable").removeAttr("title");
        var templateHtml = $("#template-editor").html();
        $http.post(rootPath + 'mailtemplate/' + templateId + '/editor_save', {
            content: templateHtml
        }).then(function (response) {
            toaster.pop('success', 'guardado');
        }, function (response) {
            $scope.editedTodo = null;
        });
    };
});


