function _toConsumableArray(e){return _arrayWithoutHoles(e)||_iterableToArray(e)||_unsupportedIterableToArray(e)||_nonIterableSpread()}function _nonIterableSpread(){throw new TypeError("Invalid attempt to spread non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.")}function _unsupportedIterableToArray(e,t){if(e){if("string"==typeof e)return _arrayLikeToArray(e,t);var a=Object.prototype.toString.call(e).slice(8,-1);return"Object"===a&&e.constructor&&(a=e.constructor.name),"Map"===a||"Set"===a?Array.from(a):"Arguments"===a||/^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(a)?_arrayLikeToArray(e,t):void 0}}function _iterableToArray(e){if("undefined"!=typeof Symbol&&Symbol.iterator in Object(e))return Array.from(e)}function _arrayWithoutHoles(e){if(Array.isArray(e))return _arrayLikeToArray(e)}function _arrayLikeToArray(e,t){(null==t||t>e.length)&&(t=e.length);for(var a=0,n=new Array(t);a<t;a++)n[a]=e[a];return n}function fmSetLink(e){$("div.trumbowyg-modal-box").find("input[name=url]").val(e)}$(function(){$(document).trigger("ready"),$(document).trigger("readyAndAjax")}),$(document).ajaxComplete(function(){$(document).trigger("readyAndAjax")}),$(document).on("readyAndAjax.n1ebieski/icore/admin/scripts/actions@enter",function(){$("form").find("input, select").on("keypress",function(e){if(13==e.which)return e.preventDefault(),!1})}),$(window).on("readyAndAjax.n1ebieski/icore/admin/scripts/actions@focusSpellcheck",function(){-1!=navigator.userAgent.indexOf("Firefox")&&$('[spellcheck="true"]:first').focusWithoutScrolling()}),$(document).on("click.n1ebieski/icore/admin/scripts/ajax/ban@store",".storeBanModel, .store-banmodel",function(e){e.preventDefault();var t=$(this),a=t.closest(".modal-content").find("form");a.btn=a.find(".btn"),a.input=a.find(".form-control, .custom-control-input"),$.ajax({url:a.attr("data-route"),method:"post",data:a.serialize(),dataType:"json",beforeSend:function(){t.loader("show"),$(".invalid-feedback").remove(),a.input.removeClass("is-valid"),a.input.removeClass("is-invalid")},complete:function(){t.loader("hide"),a.input.addClass("is-valid")},success:function(e){$(".modal").modal("hide"),$("body").addToast(e.success)},error:function(e){e.responseJSON.errors&&$.each(e.responseJSON.errors,function(e,t){a.find("#"+$.escapeSelector(e)).addClass("is-invalid"),a.find("#"+$.escapeSelector(e)).closest(".form-group").addError({id:e,message:t})})}})}),$(document).on("click.n1ebieski/icore/admin/scripts/ajax/category@destroy",".destroyCategory, .destroy-category",function(e){e.preventDefault();var t=$(this),a=$("#row"+t.data("id"));$.ajax({url:t.data("route"),method:"delete",beforeSend:function(){a.find(".responsive-btn-group").addClass("disabled"),a.find('[data-btn-ok-class*="destroyCategory"], [data-btn-ok-class*="destroy-category"]').loader("show")},complete:function(){a.find('[data-btn-ok-class*="destroyCategory"], [data-btn-ok-class*="destroy-category"]').loader("hide")},success:function(e){a.fadeOut("slow"),$.each(e.descendants,function(e,t){var a=$("#row"+t);a.length&&a.fadeOut("slow")})}})}),$(document).on("click.n1ebieski/icore/admin/scripts/ajax/category@updateStatus",".statusCategory, .status-category",function(e){e.preventDefault();var t=$(this),a=t.closest("[id^=row]");a.btnGroup=a.find(".responsive-btn-group"),a.btn0=a.find('button[data-status="0"]'),a.btn1=a.find('button[data-status="1"]'),$.ajax({url:t.data("route"),method:"patch",data:{status:t.data("status")},beforeSend:function(){a.btnGroup.addClass("disabled"),t.loader("show")},success:function(e){t.loader("hide"),1==e.status&&(a.btnGroup.removeClass("disabled"),a.btn1.prop("disabled",!0),a.btn0.attr("disabled",!1),a.addClass("alert-success"),setTimeout(function(){a.removeClassStartingWith("alert-")},5e3),$.each(e.ancestors,function(e,t){var a=$("#row"+t);a.length&&(a.find('button[data-status="1"]').prop("disabled",!0),a.find('button[data-status="0"]').attr("disabled",!1),a.addClass("alert-success"),setTimeout(function(){a.removeClassStartingWith("alert-")},5e3))})),0==e.status&&(a.btnGroup.removeClass("disabled"),a.btn0.prop("disabled",!0),a.btn1.attr("disabled",!1),a.addClass("alert-warning"),setTimeout(function(){a.removeClassStartingWith("alert-")},5e3),$.each(e.descendants,function(e,t){var a=$("#row"+t);a.length&&(a.find('button[data-status="0"]').prop("disabled",!0),a.find('button[data-status="1"]').attr("disabled",!1),a.addClass("alert-warning"),setTimeout(function(){a.removeClassStartingWith("alert-")},5e3))}))}})}),$(document).on("click.n1ebieski/icore/admin/scripts/ajax/comment@destroy",".destroyComment, .destroy-comment",function(e){e.preventDefault();var t=$(this),a=$("#row"+t.data("id"));$.ajax({url:t.data("route"),method:"delete",beforeSend:function(){a.find(".responsive-btn-group").addClass("disabled"),a.find('[data-btn-ok-class*="destroyComment"], [data-btn-ok-class*="destroy-comment"]').loader("show")},complete:function(){a.find('[data-btn-ok-class*="destroyComment"], [data-btn-ok-class*="destroy-comment"]').loader("hide")},success:function(e){a.fadeOut("slow"),$.each(e.descendants,function(e,t){var a=$("#row"+t);a.length&&a.fadeOut("slow")})}})}),$(document).on("click.n1ebieski/icore/admin/scripts/ajax/comment@store",".storeComment, .store-comment",function(e){e.preventDefault();var t=$(this),a=t.closest(".modal-content").find("form");a.btn=a.find(".btn"),a.input=a.find(".form-control"),$.ajax({url:a.data("route"),method:"post",data:a.serialize(),dataType:"json",beforeSend:function(){t.loader("show"),$(".invalid-feedback").remove(),a.input.removeClass("is-valid"),a.input.removeClass("is-invalid")},complete:function(){t.loader("hide"),a.input.addClass("is-valid")},success:function(e){var t=$("#row"+a.data("id"));t.after($.sanitize(e.view));var n=t.next();n.addClass("alert-primary font-italic"),setTimeout(function(){n.removeClassStartingWith("alert-")},5e3),$(".modal").modal("hide")},error:function(e){$.each(e.responseJSON.errors,function(e,t){a.find("#"+$.escapeSelector(e)).addClass("is-invalid"),a.find("#"+$.escapeSelector(e)).closest(".form-group").addError({id:e,message:t})})}})}),$(document).on("click.n1ebieski/icore/admin/scripts/ajax/comment@updateCensored",".censoreComment, .censore-comment",function(e){e.preventDefault();var t=$(this),a=t.closest("[id^=row]");$.ajax({url:t.data("route"),method:"patch",data:{censored:t.data("censored")},beforeSend:function(){a.find(".responsive-btn-group").addClass("disabled"),t.loader("show")},success:function(e){t.loader("hide"),a.html($.sanitize($(e.view).html())),1==e.censored&&(a.addClass("alert-warning"),setTimeout(function(){a.removeClassStartingWith("alert-")},5e3)),0==e.censored&&(a.addClass("alert-success"),setTimeout(function(){a.removeClassStartingWith("alert-")},5e3))}})}),$(document).on("click.n1ebieski/icore/admin/scripts/ajax/comment@updateStatus",".statusComment, .status-comment",function(e){e.preventDefault();var t=$(this),a=t.closest("[id^=row]");a.btnGroup=a.find(".responsive-btn-group"),a.btn0=a.find('button[data-status="0"]'),a.btn1=a.find('button[data-status="1"]'),$.ajax({url:t.data("route"),method:"patch",data:{status:t.data("status")},beforeSend:function(){a.btnGroup.addClass("disabled"),t.loader("show")},success:function(e){t.loader("hide"),1==e.status&&(a.btnGroup.removeClass("disabled"),a.btn1.prop("disabled",!0),a.btn0.prop("disabled",!1),a.find("button.answer").prop("disabled",!1),a.addClass("alert-success"),setTimeout(function(){a.removeClassStartingWith("alert-")},5e3),$.each(e.ancestors,function(e,t){var a=$("#row"+t);a.length&&(a.find('button[data-status="1"]').prop("disabled",!0),a.find('button[data-status="0"]').prop("disabled",!1),a.find("button.answer").prop("disabled",!1),a.addClass("alert-success"),setTimeout(function(){a.removeClassStartingWith("alert-")},5e3))})),0==e.status&&(a.btnGroup.removeClass("disabled"),a.btn0.prop("disabled",!0),a.btn1.prop("disabled",!1),a.find("button.answer").prop("disabled",!0),a.addClass("alert-warning"),setTimeout(function(){a.removeClassStartingWith("alert-")},5e3),$.each(e.descendants,function(e,t){var a=$("#row"+t);a.length&&(a.find('button[data-status="0"]').prop("disabled",!0),a.find('button[data-status="1"]').prop("disabled",!1),a.find("button.answer").prop("disabled",!0),a.addClass("alert-warning"),setTimeout(function(){a.removeClassStartingWith("alert-")},5e3))}))}})}),$(document).on("click.n1ebieski/icore/admin/scripts/ajax/default@create",".create",function(e){e.preventDefault();var t=$(this),a={body:$(t.data("target")).find(".modal-body"),footer:$(t.data("target")).find(".modal-footer"),content:$(t.data("target")).find(".modal-content")};a.body.empty(),a.footer.empty(),$.ajax({url:t.data("route"),method:"get",beforeSend:function(){a.body.addLoader("spinner-grow")},complete:function(){a.body.find(".loader-absolute").remove()},success:function(e){a.content.html($.sanitize($(e.view).find(".modal-content").html()))},error:function(e){e.responseJSON.message&&$("body").addToast({title:e.responseJSON.message,type:"danger"})}})}),$(document).on("click.n1ebieski/icore/admin/scripts/ajax/default@destroy",".destroy",function(e){e.preventDefault();var t=$(this),a=$("#row"+t.data("id"));$.ajax({url:t.data("route"),method:"delete",beforeSend:function(){a.find(".responsive-btn-group").addClass("disabled"),a.find('[data-btn-ok-class*="destroy"]').loader("show")},complete:function(){a.find('[data-btn-ok-class*="destroy"]').loader("hide")},success:function(e){a.fadeOut("slow")}})}),$(document).on("click.n1ebieski/icore/admin/scripts/ajax/default@edit",".edit",function(e){e.preventDefault();var t=$(this),a={body:$(t.data("target")).find(".modal-body"),footer:$(t.data("target")).find(".modal-footer"),content:$(t.data("target")).find(".modal-content")};a.body.empty(),a.footer.empty(),$.ajax({url:t.data("route"),method:"get",beforeSend:function(){a.body.addLoader("spinner-grow")},complete:function(){a.body.find(".loader-absolute").remove()},success:function(e){a.content.html($.sanitize($(e.view).find(".modal-content").html()))}})}),$(document).on("ready.n1ebieski/icore/admin/scripts/ajax/default@filter",function(){function e(e,t){$.ajax({url:t,method:"get",dataType:"html",beforeSend:function(){$("#filterContent, #filter-content").find(".btn").prop("disabled",!0),$("#filterOrderBy, #filter-orderby").prop("disabled",!0),$("#filterPaginate, #filter-paginate").prop("disabled",!0),e.children("div:first").addLoader(),$("#filterModal, #filter-modal").modal("hide")},complete:function(){e.find(".loader-absolute").remove()},success:function(e){$(".modal-backdrop").remove(),$("body").removeClass("modal-open").removeAttr("style"),$("#filterContent, #filter-content").html($.sanitize($(e).find("#filterContent, #filter-content").html())),document.title=document.title.replace(/:\s(\d+)/,": 1"),history.replaceState(null,null,t)}})}$(document).on("change.n1ebieski/icore/admin/scripts/ajax/default@filterOrderBy","#filterOrderBy, #filter-orderby",function(t){t.preventDefault();var a=$("#filter");a.href=a.data("route")+"?"+a.serialize(),e(a,a.href)}),$(document).on("click.n1ebieski/icore/admin/scripts/ajax/default@filterFilter","#filterFilter, #filter-filter",function(t){t.preventDefault();var a=$("#filter");a.href=a.data("route")+"?"+a.serialize(),$("#filter").valid()&&e(a,a.href)}),$(document).on("click.n1ebieski/icore/admin/scripts/ajax/default@filterOption","a.filterOption, a.filter-option",function(t){t.preventDefault();var a=$("#filter");a.href=a.data("route")+"?"+a.find('[name!="'+$.escapeSelector($(this).data("name"))+'"]').serialize(),e(a,a.href)}),$(document).on("change.n1ebieski/icore/admin/scripts/ajax/default@filterPaginate","#filterPaginate, #filter-paginate",function(t){t.preventDefault();var a=$("#filter");a.href=a.data("route")+"?"+a.serialize(),e(a,a.href)})}),$(document).on("click.n1ebieski/icore/admin/scripts/ajax/default@show","a.show, button.show",function(e){e.preventDefault();var t=$(this),a={body:$(t.data("target")).find(".modal-body"),footer:$(t.data("target")).find(".modal-footer"),content:$(t.data("target")).find(".modal-content")};a.body.empty(),a.footer.empty(),$.ajax({url:t.data("route"),method:"get",beforeSend:function(){a.body.addLoader("spinner-grow")},complete:function(){a.body.find(".loader-absolute").remove()},success:function(e){a.content.html($.sanitize($(e.view).find(".modal-content").html()))}})}),$(document).on("click.n1ebieski/icore/admin/scripts/ajax/default@store",".store",function(e){e.preventDefault();var t=$(this),a=t.closest(".modal-content").find("form:visible");a.btn=a.find(".btn"),a.input=a.find(".form-control"),$.ajax({url:a.data("route"),method:"post",data:new FormData(a[0]),processData:!1,contentType:!1,dataType:"json",beforeSend:function(){t.loader("show"),$(".invalid-feedback").remove(),a.input.removeClass("is-valid"),a.input.removeClass("is-invalid")},complete:function(){t.loader("hide"),a.input.addClass("is-valid")},success:function(e){$(".modal").modal("hide"),window.location.reload()},error:function(e){if(e.responseJSON.errors){var t=0;$.each(e.responseJSON.errors,function(e,n){a.find("#"+$.escapeSelector(e)).length||(e=e.match(/([a-z_\-\.]+)(?:\.([\d]+)|)$/)[1]),a.find("#"+$.escapeSelector(e)).addClass("is-invalid"),a.find("#"+$.escapeSelector(e)).closest(".form-group").addError({id:e,message:n}),0===t&&$("#"+$.escapeSelector(e)).length&&a.parent().animate({scrollTop:a.parent().scrollTop()+a.find("#"+$.escapeSelector(e)).closest(".form-group").position().top},1e3),t++})}else e.responseJSON.message&&$("body").addToast({title:e.responseJSON.message,type:"danger"})}})}),$(document).on("click.n1ebieski/icore/admin/scripts/ajax/default@updatePosition",".updatePosition, .update-position",function(e){e.preventDefault();var t=$(this),a=t.closest(".modal-content").find("form");$.ajax({url:a.data("route"),method:"patch",data:{position:a.find("#position").val()},beforeSend:function(){t.loader("show")},complete:function(){t.loader("hide")},success:function(e){$(".modal").modal("hide"),$.each(e.siblings,function(e,t){var a=$("#row"+e);a.length&&(a.find("#position").text(t+1),a.addClass("alert-primary"),setTimeout(function(){a.removeClassStartingWith("alert-")},5e3))})}})}),$(document).on("click.n1ebieski/icore/admin/scripts/ajax/default@updateStatus",".status",function(e){e.preventDefault();var t=$(this),a=t.closest("[id^=row]");$.ajax({url:t.data("route"),method:"patch",data:{status:t.data("status")},beforeSend:function(){a.find(".responsive-btn-group").addClass("disabled"),t.loader("show")},success:function(e){t.loader("hide"),a.html($.sanitize($(e.view).html())),1==e.status&&(a.addClass("alert-success"),setTimeout(function(){a.removeClassStartingWith("alert-")},5e3)),0==e.status&&(a.addClass("alert-warning"),setTimeout(function(){a.removeClassStartingWith("alert-")},5e3))}})}),$(document).on("click.n1ebieski/icore/admin/scripts/ajax/default@update",".update",function(e){e.preventDefault();var t=$(this),a=t.closest(".modal-content").find("form:visible");a.btn=a.find(".btn"),a.input=a.find(".form-control");var n=new FormData(a[0]);n.append("_method","put"),$.ajax({url:a.data("route"),method:"post",data:n,processData:!1,contentType:!1,dataType:"json",beforeSend:function(){t.loader("show"),a.find(".invalid-feedback").remove(),a.input.removeClass("is-valid"),a.input.removeClass("is-invalid")},complete:function(){t.loader("hide"),a.input.addClass("is-valid")},success:function(e){var t=$("#row"+a.attr("data-id"));t.html($.sanitize($(e.view).html())),t.addClass("alert-primary"),setTimeout(function(){t.removeClassStartingWith("alert-")},5e3),$(".modal").modal("hide")},error:function(e){if(e.responseJSON.errors){var t=0;$.each(e.responseJSON.errors,function(e,n){a.find("#"+$.escapeSelector(e)).length||(e=e.match(/([a-z_\-\.]+)(?:\.([\d]+)|)$/)[1]),a.find("#"+$.escapeSelector(e)).addClass("is-invalid"),a.find("#"+$.escapeSelector(e)).closest(".form-group").addError({id:e,message:n}),0===t&&$("#"+$.escapeSelector(e)).length&&a.parent().animate({scrollTop:a.parent().scrollTop()+a.find("#"+$.escapeSelector(e)).closest(".form-group").position().top},1e3),t++})}else e.responseJSON.message&&$("body").addToast({title:e.responseJSON.message,type:"danger"})}})}),$(document).on("click.n1ebieski/icore/admin/scripts/ajax/mailing@reset","a.resetMailing, a.reset-mailing",function(e){e.preventDefault();var t=$(this),a=$("#row"+t.data("id"));$.ajax({url:t.data("route"),method:"delete",beforeSend:function(){a.find(".responsive-btn-group").addClass("disabled"),a.find('[data-btn-ok-class*="resetMailing"], [data-btn-ok-class*="reset-mailing"]').loader("show")},complete:function(){a.find('[data-btn-ok-class*="resetMailing"], [data-btn-ok-class*="reset-mailing"]').loader("hide")},success:function(e){a.html($.sanitize($(e.view).html())),a.addClass("alert-danger"),setTimeout(function(){a.removeClassStartingWith("alert-")},5e3)}})}),$(document).on("click.n1ebieski/icore/admin/scripts/ajax/page@destroy",".destroyPage, .destroy-page",function(e){e.preventDefault();var t=$(this),a=$("#row"+t.data("id"));$.ajax({url:t.data("route"),method:"delete",beforeSend:function(){a.find(".responsive-btn-group").addClass("disabled"),a.find('[data-btn-ok-class*="destroyPage"], [data-btn-ok-class*="destroy-page"]').loader("show")},complete:function(){a.find('[data-btn-ok-class*="destroyPage"], [data-btn-ok-class*="destroy-page"]').loader("hide")},success:function(e){a.fadeOut("slow"),$.each(e.descendants,function(e,t){var a=$("#row"+t);a.length&&a.fadeOut("slow")})}})}),$(document).on("click.n1ebieski/icore/admin/scripts/ajax/page@updateStatus",".statusPage, .status-page",function(e){e.preventDefault();var t=$(this),a=t.closest("[id^=row]");a.btnGroup=a.find(".responsive-btn-group"),a.btn0=a.find('button[data-status="0"]'),a.btn1=a.find('button[data-status="1"]'),$.ajax({url:t.data("route"),method:"patch",data:{status:t.data("status")},beforeSend:function(){a.btnGroup.addClass("disabled"),t.loader("show")},success:function(e){t.loader("hide"),1==e.status&&(a.btnGroup.removeClass("disabled"),a.btn1.prop("disabled",!0),a.btn0.prop("disabled",!1),a.addClass("alert-success"),setTimeout(function(){a.removeClassStartingWith("alert-")},5e3),$.each(e.ancestors,function(e,t){var a=$("#row"+t);a.length&&(a.find('button[data-status="1"]').prop("disabled",!0),a.find('button[data-status="0"]').prop("disabled",!1),a.addClass("alert-success"),setTimeout(function(){a.removeClassStartingWith("alert-")},5e3))})),0==e.status&&(a.btnGroup.removeClass("disabled"),a.btn0.prop("disabled",!0),a.btn1.prop("disabled",!1),a.addClass("alert-warning"),setTimeout(function(){a.removeClassStartingWith("alert-")},5e3),$.each(e.descendants,function(e,t){var a=$("#row"+t);a.length&&(a.find('button[data-status="0"]').prop("disabled",!0),a.find('button[data-status="1"]').prop("disabled",!1),a.addClass("alert-warning"),setTimeout(function(){a.removeClassStartingWith("alert-")},5e3))}))}})}),$(document).on("click.n1ebieski/icore/admin/scripts/ajax/page@clear",".clearReport, .clear-report",function(e){e.preventDefault();var t=$(this);$.ajax({url:t.data("route"),method:"delete",beforeSend:function(){t.loader("show")},complete:function(){t.loader("hide")},success:function(e){var a=$("#row"+t.attr("data-id"));a.html($.sanitize($(e.view).html())),a.addClass("alert-primary"),setTimeout(function(){a.removeClassStartingWith("alert-")},5e3),$(".modal").modal("hide")}})}),function(e){e.fn.chart=function(e){return new Chart(this,e)},e.fn.serializeObject=function(){var t=this,a={},n={},i={validate:/^[a-zA-Z][a-zA-Z0-9_]*(?:\[(?:\d*|[a-zA-Z0-9_]+)\])*$/,key:/[a-zA-Z0-9_]+|(?=\[\])/g,push:/^$/,fixed:/^\d+$/,named:/^[a-zA-Z0-9_]+$/};return this.build=function(e,t,a){return e[t]=a,e},this.push_counter=function(e){return void 0===n[e]&&(n[e]=0),n[e]++},e.each(e(this).serializeArray(),function(){if(i.validate.test(this.name)){for(var n,o=this.name.match(i.key),s=this.value,r=this.name;void 0!==(n=o.pop());)r=r.replace(new RegExp("\\["+n+"\\]$"),""),n.match(i.push)?s=t.build([],t.push_counter(r),s):n.match(i.fixed)?s=t.build([],n,s):n.match(i.named)&&(s=t.build({},n,s));a=e.extend(!0,a,s)}}),a},e.fn.autoHeight=function(t){function a(t){return t.offsetHeight<t.scrollHeight?e(t).css({height:"auto"}).height(t.scrollHeight):e(t)}if(!1!==("boolean"!=typeof t.autogrow||t.autogrow))return this.each(function(){a(this).on("input",function(){a(this)})})},e.fn.removeClassStartingWith=function(e){this.removeClass(function(t,a){return(a.match(new RegExp("\\b"+e+"\\S+","g"))||[]).join(" ")})},e.fn.focusWithoutScrolling=function(){var e=window.scrollX,t=window.scrollY;this.focus(),window.scrollTo(e,t)},e.sanitize=function(t){var a=e(e.parseHTML("<div>"+t+"</div>",null,!1));return a.find("*").each(function(t,a){e.each(a.attributes,function(){var t=this.name,n=this.value;0!=t.indexOf("on")&&0!=n.indexOf("javascript:")||e(a).removeAttr(t)})}),a.html()},e.getUrlParameter=function(e,t){return(RegExp(t+"=(.+?)(&|$)").exec(e)||[,null])[1]},e.fn.loader=function(t){var a=arguments.length>1&&void 0!==arguments[1]?arguments[1]:"spinner-border";if("show"==t){e(this).parent().find("button").prop("disabled",!0),e(this).find("i").hide();var n=!e(this).is('[class*="btn-outline-"]')||void 0!==e.cookie("theme_toggle")&&"light"!==e.cookie("theme_toggle")?"text-light":"text-dark";e(this).prepend(e.sanitize('<span class="'+a+" "+a+"-sm "+n+'" role="status" aria-hidden="true"></span>'))}"hide"==t&&(e(this).parent().find("button").prop("disabled",!1),e(this).find("i").show(),e(this).find('[role="status"]').remove())},e.fn.addLoader=function(){var t,a=arguments.length>0&&void 0!==arguments[0]?arguments[0]:null;return a={type:"string"==typeof a?a:"spinner-border",class:(null===(t=a)||void 0===t?void 0:t.class)||"loader-absolute"},this.append(e.sanitize('\n            <div class="'.concat(a.class,'">\n                <div class="').concat(a.type,'">\n                    <span class="sr-only">Loading...</span>\n                </div>\n            </div>\n        ')))},e.fn.addAlert=function(t){return t={message:"string"==typeof t?t:t.message,type:t.type||"danger"},this.prepend(e.sanitize('\n            <div class="alert alert-'.concat(t.type,' alert-time" role="alert">\n                <button \n                    type="button" \n                    class="text-dark close" \n                    data-dismiss="alert" \n                    aria-label="Close"\n                >\n                    <span aria-hidden="true">&times;</span>\n                </button>\n                ').concat(t.message,"\n            </div>\n        ")))},e.fn.addToast=function(t){t={title:"string"==typeof t?t:t.title,type:t.type||"success",message:t.message||""};var a=e(e.sanitize('\n            <div>\n                <div \n                    class="toast bg-'.concat(t.type,'"\n                    role="alert" \n                    aria-live="assertive" \n                    aria-atomic="true" \n                    data-delay="20000" \n                >\n                    <div class="toast-header">\n                        <strong class="mr-auto">').concat(t.title,'</strong>\n                        <button \n                            type="button" \n                            class="text-dark ml-2 mb-1 close" \n                            data-dismiss="toast" \n                            aria-label="Close"\n                        >\n                            <span aria-hidden="true">&times;</span>\n                        </button>\n                    </div>             \n                </div>\n            </div>\n        ')));return t.message.length&&a.find(".toast").append(e.sanitize('\n                <div class="toast-body bg-light text-dark">\n                    '.concat(t.message,"\n                </div>\n            "))),this.append(a.html())},e.fn.addError=function(t){t={message:"string"==typeof t?t:t.message,id:t.id||null};var a=e(e.sanitize('\n            <div>\n                <span class="invalid-feedback d-block font-weight-bold">'.concat(t.message,"</span>\n            </div>\n        ")));return null!==t.id&&a.find(".invalid-feedback").attr("id",t.id),this.append(a.html())}}(jQuery),$(document).on("readyAndAjax.n1ebieski/icore/admin/scripts/plugins/bootstrap-confirmation@init",function(){$("[data-toggle=confirmation]").each(function(){var e=$(this);e.confirmation({rootSelector:"[data-toggle=confirmation]",copyAttributes:"href data-route data-id",singleton:!0,popout:!0,onConfirm:function(){e.hasClass("submit")&&e.parents("form:first").submit()}})})}),$(document).on("readyAndAjax.n1ebieski/icore/admin/scripts/plugins/bootstrap-select/category@init",function(){$("select.select-picker-category").each(function(){var e=$(this);!0!==e.data("loaded")&&(e.selectpicker().on("changed.bs.select",function(){e.next("button").find(".filter-option-inner-inner > small").remove()}).on("shown.bs.select",function(){e.parent().find(".dropdown-menu").find('input[type="search"]').attr("name","search")}).trigger("change"),!0===e.data("abs")&&(e.ajaxSelectPicker({ajax:{data:function(){return{filter:{search:"{{{q}}}",orderby:"real_depth|desc",except:e.data("abs-filter-except")||null,status:1}}}},preprocessData:function(t){var a=[],n=e.data("abs-max-options-length")||t.data.length,i=e.data("abs-default-options")||[];return $.each(i,function(e,t){a.push({value:t.value,text:t.text})}),$.each(t.data,function(t,i){if(t>=n)return!1;a.push({value:e.data("abs-value-attr")?i[e.data("abs-value-attr")]:i.id,text:e.data("abs-text-attr")?i[e.data("abs-text-attr")]:i.name,data:{content:i.ancestors.length?'<small class="p-0 m-0">'+i.ancestors.map(function(e){return e.name}).join(" &raquo; ")+" &raquo; </small>"+i.name:null}})}),a},minLength:3,preserveSelected:"boolean"!=typeof e.data("abs-preserve-selected")||e.data("abs-preserve-selected"),preserveSelectedPosition:e.data("abs-preserve-selected-position")||"before",langCode:e.data("abs-lang-code")||null}),e.trigger("change").data("AjaxBootstrapSelect").list.cache={}),e.parent().addClass("input-group"),e.attr("data-loaded",!0))})}),jQuery(document).on("readyAndAjax.n1ebieski/icore/admin/scripts/plugins/bootstrap-select/default@init",function(){$("select.select-picker").each(function(){var e=$(this);!0!==e.data("loaded")&&(e.selectpicker().on("changed.bs.select",function(){e.next("button").find(".filter-option-inner-inner > small").remove()}).on("shown.bs.select",function(){e.parent().find(".dropdown-menu").find('input[type="search"]').attr("name","search")}).trigger("change"),!0===e.data("abs")&&(e.ajaxSelectPicker({ajax:{data:function(){return{filter:{search:"{{{q}}}",except:e.data("abs-filter-except")||null,status:1}}}},preprocessData:function(t){var a=[],n=e.data("abs-max-options-length")||t.data.length,i=e.data("abs-default-options")||[];return $.each(i,function(e,t){a.push({value:t.value,text:t.text})}),$.each(t.data,function(t,i){if(t>=n)return!1;a.push({value:e.data("abs-value-attr")?i[e.data("abs-value-attr")]:i.id,text:e.data("abs-text-attr")?i[e.data("abs-text-attr")]:i.name})}),a},minLength:3,preserveSelected:"boolean"!=typeof e.data("abs-preserve-selected")||e.data("abs-preserve-selected"),preserveSelectedPosition:e.data("abs-preserve-selected-position")||"before",langCode:e.data("abs-lang-code")||null}),e.trigger("change").data("AjaxBootstrapSelect").list.cache={}),e.parent().addClass("input-group"),e.attr("data-loaded",!0))})}),$(document).on("ready.n1ebieski/icore/admin/scripts/plugins/chart/post@countByDate",function(){var e=$("#count-posts-and-pages-by-date");if(e.length){e.dataset=JSON.parse(e.attr("data"));var t=_toConsumableArray(new Map(e.dataset.map(function(e){return["".concat(e.month,".").concat(e.year),e]})).values()),a=_toConsumableArray(new Map(e.dataset.map(function(e){return[e.type.value,e]})).values()),n=0;if(e.chart({type:"bar",data:{datasets:[{label:e.data("all-label"),type:"line",backgroundColor:"rgb(0, 123, 255)",borderColor:"rgb(0, 123, 255)",borderWidth:1,data:t.map(function(t){return{x:"".concat(t.month,".").concat(t.year),y:e.dataset.filter(function(e){return e.month===t.month&&e.year===t.year}).reduce(function(e,t){return n=e+t.count},n)}})}].concat(a.map(function(a){return{label:a.type.label,data:t.map(function(t){var n;return(null===(n=e.dataset.find(function(e){return e.month===t.month&&e.year===t.year&&e.type.value===a.type.value}))||void 0===n?void 0:n.count)||0}),backgroundColor:a.color,borderColor:a.color,borderWidth:1}}))},options:{responsive:!0,maintainAspectRatio:!1,scales:{x:{stacked:!0,title:{color:e.data("font-color")||"#666",display:!0,text:e.data("x-label")},ticks:{color:e.data("font-color")||"#666"}},y:{stacked:!0,title:{color:e.data("font-color")||"#666",display:!0,text:e.data("y-label")},ticks:{color:e.data("font-color")||"#666"}}},plugins:{legend:{labels:{color:e.data("font-color")||"#666"}},title:{display:!0,text:e.data("label"),color:e.data("font-color")||"#666",font:{size:14}}}}}),t.length>15){var i=50*t.length;e.parent().css("width",i),e.parents().eq(1).scrollLeft(i)}}}),$(document).on("readyAndAjax.n1ebieski/icore/admin/scripts/plugins/infinite-scroll@init",function(){var e=$("#infinite-scroll");e.jscroll({debug:!1,autoTrigger:1==e.data("autotrigger"),data:function(){var e=$("#filter").serializeObject().filter||{};if(e.except=$(this).find("[id^=row]").map(function(){return $(this).attr("data-id")}).get(),e.except.length)return{filter:e}},loadingHtml:'<div class="loader"><div class="spinner-border"><span class="sr-only">Loading...</span></div></div>',loadingFunction:function(){$("#is-pagination").first().remove()},padding:0,nextSelector:"a#is-next:last",contentSelector:"#infinite-scroll",pagingSelector:".pagination",callback:function(e){var t=e.split(" ")[0];history.replaceState(null,null,t)}})}),$(document).on("readyAndAjax.n1ebieski/icore/admin/scripts/plugins/jquery-lazy@init",function(){$(".lazy").lazy({effect:"fadeIn",effectTime:"fast",threshold:0})}),$(document).on("readyAndAjax.n1ebieski/icore/admin/scripts/plugins/magnific-popup@init",function(){var e=$(".lightbox");if(e.length){var t=e.map(function(){return $(this).data("gallery")}).get().filter(function(e,t,a){return t==a.indexOf(e)});$.each(t,function(e,t){$("[data-gallery="+$.escapeSelector(t)+"]").magnificPopup({type:"image",gallery:{enabled:!0}})})}}),$(document).on("ready.n1ebieski/icore/admin/scripts/plugins/pickadate@init",function(){"pl"===$(".datepicker, .timepicker").data("lang")&&($.extend($.fn.pickadate.defaults,{monthsFull:["styczeń","luty","marzec","kwiecień","maj","czerwiec","lipiec","sierpień","wrzesień","październik","listopad","grudzień"],monthsShort:["sty","lut","mar","kwi","maj","cze","lip","sie","wrz","paź","lis","gru"],weekdaysFull:["niedziela","poniedziałek","wtorek","środa","czwartek","piątek","sobota"],weekdaysShort:["niedz.","pn.","wt.","śr.","cz.","pt.","sob."],today:"Dzisiaj",clear:"Usuń",close:"Zamknij",firstDay:1,format:"d mmmm yyyy",formatSubmit:"yyyy/mm/dd"}),$.extend($.fn.pickatime.defaults,{clear:"usunąć"})),$("form#createPost .datepicker, form#editFullPost .datepicker, form#create-post .datepicker, form#editfull-post .datepicker").pickadate({clear:"",formatSubmit:"yyyy-m-dd",hiddenName:!0}),$("form#createMailing .datepicker, form#editMailing .datepicker, form#create-mailing .datepicker, form#edit-mailing .datepicker").pickadate({clear:"",formatSubmit:"yyyy-m-dd",hiddenName:!0,min:new Date}),$(".timepicker").pickatime({clear:"",format:"H:i",formatSubmit:"HH:i",hiddenName:!0})}),$(document).on("readyAndAjax.n1ebieski/icore/admin/scripts/plugins/tagsinput@init",function(){$(".tagsinput").each(function(){var e=$(this);e.parent().find('[id$="_tagsinput"]').length||e.tagsInput({placeholder:e.attr("placeholder"),minChars:3,maxChars:e.data("max-chars")||30,limit:e.data("max"),validationPattern:new RegExp("^(?:^[a-zA-ZąćęłńóśźżĄĆĘŁŃÓŚŹŻ0-9à-ü ]+$)$"),unique:!0})})}),$(document).on("readyAndAjax.n1ebieski/icore/admin/scripts/plugins/trumbowyg@init",function(){if(!$(".trumbowyg-box").length){var e=$("#content_html_trumbowyg");e.trumbowyg({lang:e.data("lang"),fixedBtnPane:e.data("fixed-btn-pane")||!0,fixedFullWidth:e.data("fixed-full-width")||!1,svgPath:!1,hideButtonTexts:!0,tagsToRemove:["script"],autogrow:!0,btnsDef:{more:{fn:function(){e.trumbowyg("execCmd",{cmd:"insertHtml",param:"<p>[more]</p>",forceCss:!1})},title:'Button "show more"',ico:"more"}},btns:[["viewHTML"],["historyUndo","historyRedo"],["formatting"],["foreColor","backColor"],["strong","em","del"],["superscript","subscript"],["link"],["insertImage"],["table"],["justifyLeft","justifyCenter","justifyRight","justifyFull"],["unorderedList","orderedList"],["horizontalRule"],["removeformat"],["more"],["fullscreen"]]}),e.on("tbwmodalopen",function(){var e=$("div.trumbowyg-modal-box");e.input=e.find("input[name=url]"),e.input.length&&e.find("input[name=alt]").length&&(e.input.css({position:"initial",width:"50px",flex:"auto",order:"1"}),e.input.wrap('<div style="position:absolute;top:0;right:0;width:70%;max-width:330px;"><div class="input-group" style="display:flex;">'),e.input.after('<div class="input-group-append" style="order:2;"><button class="btn btn-primary px-2 py-0" type="button" id="filemanager" style="height:27px;"><i class="far fa-image"></i></button></div>'))}),e.on("tbwopenfullscreen",function(){$(".trumbowyg-fullscreen .trumbowyg-editor").css({cssText:"height: calc(100% - ".concat($(".trumbowyg-button-pane").height(),"px) !important")})}),$(document).on("click.n1ebieski/icore/admin/scripts/plugins/trumbowyg@fileManager","button#filemanager",function(e){e.preventDefault(),window.open("/admin/file-manager/fm-button","fm","resizable=yes,status=no,scrollbars=yes,toolbar=no,menubar=no,width=1366,height=768")})}}),$(document).on("readyAndAjax.n1ebieski/icore/admin/scripts/view/alerts@init",function(){$(".alert-time").delay(2e4).fadeOut()}),$(document).on("readyAndAjax.n1ebieski/icore/admin/scripts/view/bootstrap_tooltips@init",function(){$('[data-toggle="tooltip"]').tooltip()}),$(document).on("change.n1ebieski/icore/admin/scripts/view/collapse@publishedAt",'[aria-controls="collapse-published-at"]',function(){0==$(this).val()?$("#collapse-published-at").collapse("hide"):$("#collapse-published-at").collapse("show")}),$(document).on("change.n1ebieski/icore/admin/scripts/view/collapse@activationAt",'[aria-controls="collapse-activation-at"]',function(){2==$(this).val()?$("#collapse-activation-at").collapse("show"):$("#collapse-activation-at").collapse("hide")}),$(document).on("readyAndAjax.n1ebieski/icore/admin/scripts/view/counter@init",function(){$(".counter").each(function(){var e=$(this);e.name=$.escapeSelector(e.data("name")),e.min=void 0!==e.data("min")&&Number.isInteger(e.data("min"))?e.data("min"):null,e.max=void 0!==e.data("max")&&Number.isInteger(e.data("max"))?e.data("max"):null;var t=function(){var t=[$('[name="'+e.name+'"]'),$('[name="'+e.name+'"]').hasClass("trumbowyg-textarea")?$('[name="'+e.name+'"]').parent().find(".trumbowyg-editor"):null];$.each(t.filter(function(e){return null!=e}),function(){$(this).on("keyup",function(){var t=$(this).attr("contenteditable")?parseFloat($(this).text().length):parseFloat($($.parseHTML($(this).val())).text().length);e.firstchild=e.children(":first"),e.firstchild.text(t),0===t?e.firstchild.removeClass():(e.firstchild.addClass("text-success"),e.firstchild.removeClass("text-danger"),(null!==e.min&&t<e.min||null!==e.max&&t>e.max)&&(e.firstchild.addClass("text-danger"),e.firstchild.removeClass("text-success")))})})};-1!==$('[name="'+e.name+'"]').attr("id").indexOf("trumbowyg")?$("#"+$('[name="'+e.name+'"]').attr("id")).on("tbwinit",function(){return t()}):t()})}),$(document).on("readyAndAjax.n1ebieski/icore/admin/scripts/view/custom-file-input@init",function(){$(".custom-file-input").each(function(){$(this).on("change",function(){for(var e=[],t=0;t<$(this)[0].files.length;t++)e.push($(this)[0].files[t].name);$(this).siblings(".custom-file-label").addClass("selected").html(e.join(", "))})})}),$(document).on("change.n1ebieski/icore/admin/scripts/view/delete_img@disable","form input[id^=delete_img]",function(){var e=$(this).closest(".form-group").find('[type="file"]'),t=$(this).closest(".form-group").find('[type="hidden"]');!0===$(this).prop("checked")?(e.prop("disabled",!1),t.prop("disabled",!0)):(e.prop("disabled",!0),t.prop("disabled",!1))}),$(document).on("click.n1ebieski/icore/admin/scripts/view/list_checkbox@selectAll","#selectAll, #select-all",function(){$("#selectForm .select, #select-form .select").prop("checked",$(this).prop("checked")).trigger("change")}),$(document).on("change.n1ebieski/icore/admin/scripts/view/list_checkbox@select","#selectForm .select, #select-form .select",function(){$("#selectForm .select:checked, #select-form .select:checked").length>0?$(".select-action").fadeIn():$(".select-action").fadeOut()}),$(document).on("ready.n1ebieski/icore/admin/scripts/view/navbar@init",function(){var e=$(window).scrollTop(),t=0,a=$(".navbar");!1!==a.data("autohide")&&$(window).on("scroll",function(){if(!$("body").hasClass("modal-open")){if("fixed"===$(".trumbowyg-button-pane").css("position"))return void a.fadeOut();var n=$(window).scrollTop(),i=a.height()+10;e<(t=n)&&e>i?a.fadeOut():a.fadeIn(),e=t}})}),$(document).on("scroll.n1ebieski/icore/admin/scripts/view/scroll_to_top@init",function(){$(this).scrollTop()>100?$(".scroll-to-top").fadeIn():$(".scroll-to-top").fadeOut()}),$(document).on("click.n1ebieski/icore/admin/scripts/view/scroll_to_top@scroll","a.scroll-to-top",function(e){$("html, body").stop().animate({scrollTop:0},1e3,"easeInOutExpo"),e.preventDefault()}),$(document).on("click.n1ebieski/icore/admin/scripts/view/sidebar@init",".modal-backdrop, #sidebar-toggle",function(e){e.preventDefault(),window.innerWidth>=768?($(".sidebar").toggleClass("toggled"),$("ul.sidebar").hasClass("toggled")?$.cookie("sidebar_toggle",1,{path:"/admin"}):$.cookie("sidebar_toggle",0,{path:"/admin"})):($(".sidebar").removeClass("toggled"),$(".modal-backdrop").length?($(".modal-backdrop").fadeOut("slow",function(){$(this).remove()}),$(".sidebar").removeClass("show"),$("body").removeClass("modal-open")):($('<div class="modal-backdrop show z-900"></div>').appendTo("body").hide().fadeIn(),$(".sidebar").addClass("show"),$("body").addClass("modal-open")))}),$(document).on("readyAndAjax.n1ebieski/icore/admin/scripts/view/textarea@init",function(){$("textarea").each(function(){$(this).hasClass("trumbowyg-textarea")||$(this).is("[id*=trumbowyg]")||$(this).autoHeight({autogrow:$(this).data("autogrow")})})}),$(document).on("click.n1ebieski/icore/admin/scripts/view/theme@toggle","div#themeToggle button, div#theme-toggle button",function(e){e.preventDefault();var t=$(this);t.hasClass("btn-light")&&$.cookie("theme_toggle","light",{path:"/",expires:365}),t.hasClass("btn-dark")&&$.cookie("theme_toggle","dark",{path:"/",expires:365}),window.location.reload()}),$(document).on("readyAndAjax.n1ebieski/icore/admin/scripts/view/toasts@init",function(){$(".toast").toast("show"),$(".toast").on("hidden.bs.toast",function(){$(this).remove()})});
