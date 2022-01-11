$(function(){$(document).trigger("ready"),$(document).trigger("readyAndAjax")}),$(document).ajaxComplete(function(){$(document).trigger("readyAndAjax")}),$(document).on("readyAndAjax.n1ebieski/icore/web/scripts/actions@enter",function(){$("form").find("input, select").on("keypress",function(e){if(13==e.which)return e.preventDefault(),!1})}),$(document).on("click.n1ebieski/icore/web/scripts/ajax/comment@create","a.createComment, a.create-comment",function(e){e.preventDefault();var t=$(this),a=t.closest("[id^=comment]").find("form#createComment, form#create-comment");if(a.length>0)a.fadeToggle();else{var n=t.closest("[id^=comment]");$.ajax({url:t.data("route"),method:"get",beforeSend:function(){t.prop("disabled",!0),n.addLoader({type:"spinner-border",class:"loader"})},complete:function(){t.prop("disabled",!1),n.find(".loader").remove(),n.find(".captcha").recaptcha()},success:function(e){n.children("div").append($.sanitize(e.view))},error:function(e){e.responseJSON.message&&n.parent().addAlert(e.responseJSON.message)}})}}),$(document).on("click.n1ebieski/icore/web/scripts/ajax/comment@edit","a.editComment, a.edit-comment",function(e){e.preventDefault();var t=$(this),a=t.closest("[id^=comment]");$.ajax({url:t.data("route"),method:"get",beforeSend:function(){a.children("div").hide(),a.addLoader({type:"spinner-border",class:"loader"})},complete:function(){a.find(".loader").remove()},success:function(e){a.append($.sanitize(e.view))},error:function(e){a.children("div").show(),e.responseJSON.message&&a.parent().addAlert(e.responseJSON.message)}})}),$(document).on("click.n1ebieski/icore/web/scripts/ajax/comment@cancel","button.editCommentCancel, button.edit-comment-cancel",function(e){e.preventDefault();var t=$(this).closest("[id^=comment]");t.children("div").show(),t.find("form#editComment, form#edit-comment").remove()}),$(document).on("ready.n1ebieski/icore/web/scripts/ajax/comment@filter",function(){$(document).on("change.n1ebieski/icore/web/scripts/ajax/comment@filterOrderBy","#filterCommentOrderBy, #filter-orderby-comment",function(e){e.preventDefault();var t=$("#filter");t.href=t.data("route")+"?"+t.serialize(),function(e,t){$.ajax({url:t,method:"get",dataType:"html",beforeSend:function(){$("#filterContent, #filter-content").find(".btn").prop("disabled",!0),$("#filterOrderBy, #filter-orderby").prop("disabled",!0),$("#filterPaginate, #filter-paginate").prop("disabled",!0),e.children("div").addLoader(),$("#filterModal, #filter-modal").modal("hide")},complete:function(){e.find(".loader-absolute").remove(),$("div#comment").find(".captcha").recaptcha()},success:function(e){$("#filterContent, #filter-content").html($.sanitize($(e).find("#filterContent, #filter-content").html())),document.title=document.title.replace(/:\s(\d+)/,": 1"),history.replaceState(null,null,t)}})}(t,t.href)})}),$(document).on("click.n1ebieski/icore/web/scripts/ajax/comment@rate","a.rateComment, a.rate-comment",function(e){e.preventDefault();var t=$(this),a=t.closest("[id^=comment]").find("span.rating");$.ajax({url:t.data("route"),method:"get",complete:function(){a.addClass("font-weight-bold")},success:function(e){a.text(e.sum_rating)}})}),$(document).on("click.n1ebieski/icore/web/scripts/ajax/comment@store",".storeComment, .store-comment",function(e){e.preventDefault();var t=$(this),a=t.closest("form");a.btn=a.find(".btn"),a.input=a.find(".form-control"),$.ajax({url:a.data("route"),method:"post",data:a.serialize(),dataType:"json",beforeSend:function(){t.loader("show"),$(".invalid-feedback").remove(),a.input.removeClass("is-valid"),a.input.removeClass("is-invalid")},complete:function(){t.loader("hide"),a.input.addClass("is-valid"),a.find(".captcha").recaptcha(),a.find(".captcha").captcha()},success:function(e){if(e.view){a.closest("[id^=comment]").after($.sanitize(e.view));var t=a.closest("[id^=comment]").next("div");t.addClass("alert-primary font-italic border-bottom"),setTimeout(function(){t.removeClassStartingWith("alert-")},5e3)}e.success&&a.parent().addAlert({message:e.success,type:"success"}),0!=a.find("#parent_id").val()?a.remove():a.find("#content").val("")},error:function(e){e.responseJSON.errors?$.each(e.responseJSON.errors,function(e,t){a.find("#"+$.escapeSelector(e)).addClass("is-invalid"),a.find("#"+$.escapeSelector(e)).closest(".form-group").addError({id:e,message:t})}):e.responseJSON.message&&a.addAlert(e.responseJSON.message)}})}),$(document).on("click.n1ebieski/icore/web/scripts/ajax/comment@take","a.takeComment, a.take-comment",function(e){e.preventDefault();var t=$(this),a=t.closest("[id^=row]"),n=t.closest("div");$.ajax({url:t.data("route"),method:"post",data:{filter:{except:a.children("[id^=row]").map(function(){return $(this).attr("data-id")}).get(),orderby:t.closest("#filterContent, #filter-content").find("#filterCommentOrderBy, #filter-orderby-comment").val()}},beforeSend:function(){t.hide(),n.addLoader({type:"spinner-border",class:"loader"})},complete:function(){n.find(".loader").remove()},success:function(e){a.append($.sanitize(e.view))}})}),$(document).on("click.n1ebieski/icore/web/scripts/ajax/comment@update",".updateComment, .update-comment",function(e){e.preventDefault();var t=$(this),a=t.closest("form");a.btn=a.find(".btn"),a.input=a.find(".form-control"),$.ajax({url:a.data("route"),method:"put",data:a.serialize(),dataType:"json",beforeSend:function(){t.loader("show"),$(".invalid-feedback").remove(),a.input.removeClass("is-valid"),a.input.removeClass("is-invalid")},complete:function(){t.loader("hide"),a.input.addClass("is-valid")},success:function(e){var t=a.closest("[id^=comment]");t.html($.sanitize($(e.view).html())),t.addClass("alert-primary"),setTimeout(function(){t.removeClassStartingWith("alert-")},5e3)},error:function(e){e.responseJSON.errors?$.each(e.responseJSON.errors,function(e,t){a.find("#"+$.escapeSelector(e)).addClass("is-invalid"),a.find("#"+$.escapeSelector(e)).closest(".form-group").addError({id:e,message:t})}):e.responseJSON.message&&a.addAlert(e.responseJSON.message)}})}),$(document).on("click.n1ebieski/icore/web/scripts/ajax/default@destroy",".destroy",function(e){e.preventDefault();var t=$(this),a=$("#row"+t.data("id"));$.ajax({url:t.data("route"),method:"delete",beforeSend:function(){a.find(".responsive-btn-group").addClass("disabled"),a.find('[data-btn-ok-class*="destroy"]').loader("show")},complete:function(){a.find('[data-btn-ok-class*="destroy"]').loader("hide")},success:function(e){a.fadeOut("slow")}})}),$(document).on("ready.n1ebieski/icore/web/scripts/ajax/default@filter",function(){function e(e,t){$.ajax({url:t,method:"get",dataType:"html",beforeSend:function(){$("#filterContent, #filter-content").find(".btn").prop("disabled",!0),$("#filterOrderBy, #filter-orderby").prop("disabled",!0),$("#filterPaginate, #filter-paginate").prop("disabled",!0),e.children("div").addLoader(),$("#filterModal, #filter-modal").modal("hide")},complete:function(){e.find(".loader-absolute").remove()},success:function(e){$(".modal-backdrop").remove(),$("body").removeClass("modal-open").removeAttr("style"),$("#filterContent, #filter-content").html($.sanitize($(e).find("#filterContent, #filter-content").html())),document.title=document.title.replace(/:\s(\d+)/,": 1"),history.replaceState(null,null,t)}})}$(document).on("change.n1ebieski/icore/web/scripts/ajax/default@filterOderBy","#filterOrderBy, #filter-orderby",function(t){t.preventDefault();var a=$("#filter");a.href=a.data("route")+"?"+a.serialize(),e(a,a.href)}),$(document).on("click.n1ebieski/icore/web/scripts/ajax/default@filterFilter","#filterFilter, #filter-filter",function(t){t.preventDefault();var a=$("#filter");a.href=a.data("route")+"?"+a.serialize(),$("#filter").valid()&&e(a,a.href)}),$(document).on("click.n1ebieski/icore/web/scripts/ajax/default@filterOption","a.filterOption, a.filter-option",function(t){t.preventDefault();var a=$("#filter");a.href=a.data("route")+"?"+a.find("[name!="+$.escapeSelector($(this).data("name"))+"]").serialize(),e(a,a.href)}),$(document).on("change.n1ebieski/icore/web/scripts/ajax/default@filterPaginate","#filterPaginate, #filter-paginate",function(t){t.preventDefault();var a=$("#filter");a.href=a.data("route")+"?"+a.serialize(),e(a,a.href)})}),$(document).on("click.n1ebieski/icore/web/scripts/ajax/newsletter@store",".storeNewsletter, .store-newsletter",function(e){e.preventDefault();var t=$(this),a=t.parents("form");a.btn=a.find(".btn"),a.group=a.find(".form-group"),a.input=a.find(".form-control, .custom-control-input"),$.ajax({url:a.data("route"),method:"post",data:a.serialize(),dataType:"json",beforeSend:function(){t.loader("show"),$(".invalid-feedback").remove(),$(".valid-feedback").remove(),a.input.removeClass("is-valid"),a.input.removeClass("is-invalid")},complete:function(){t.loader("hide"),a.input.addClass("is-valid")},success:function(e){e.success&&(a.find('[name="email"]').val(""),a.find('[name="email"]').closest(".form-group").addMessage(e.success))},error:function(e){e.responseJSON.errors&&$.each(e.responseJSON.errors,function(e,t){a.find('[name="'+e+'"]').addClass("is-invalid"),a.find('[name="'+e+'"]').closest(".form-group").addError({id:e,message:t})})}})}),$(document).on("click.n1ebieski/icore/web/scripts/ajax/report@create","a.createReport, a.create-report",function(e){e.preventDefault();var t=$(this),a={body:$(t.attr("data-target")).find(".modal-body"),footer:$(t.data("target")).find(".modal-footer"),content:$(t.attr("data-target")).find(".modal-content")};a.body.empty(),a.footer.empty(),$.ajax({url:t.data("route"),method:"get",beforeSend:function(){a.body.addLoader("spinner-grow")},complete:function(){a.content.find(".loader-absolute").remove()},success:function(e){a.content.html($.sanitize($(e.view).find(".modal-content").html()))}})}),$(document).on("click.n1ebieski/icore/web/scripts/ajax/report@store",".storeReport, .store-report",function(e){e.preventDefault();var t=$(this),a=t.closest(".modal-content").find("form");a.btn=a.find(".btn"),a.input=a.find(".form-control"),$.ajax({url:a.data("route"),method:"post",data:a.serialize(),dataType:"json",beforeSend:function(){t.loader("show"),$(".invalid-feedback").remove(),a.input.removeClass("is-valid"),a.input.removeClass("is-invalid")},complete:function(){t.loader("hide"),a.input.addClass("is-valid")},success:function(e){$(".modal").modal("hide"),$("body").addToast(e.success)},error:function(e){var t=e.responseJSON;$.each(t.errors,function(e,t){a.find("#"+$.escapeSelector(e)).addClass("is-invalid"),a.find("#"+$.escapeSelector(e)).closest(".form-group").addError({id:e,message:t})})}})}),function(e){e.fn.serializeObject=function(){var t=this,a={},n={},i={validate:/^[a-zA-Z][a-zA-Z0-9_]*(?:\[(?:\d*|[a-zA-Z0-9_]+)\])*$/,key:/[a-zA-Z0-9_]+|(?=\[\])/g,push:/^$/,fixed:/^\d+$/,named:/^[a-zA-Z0-9_]+$/};return this.build=function(e,t,a){return e[t]=a,e},this.push_counter=function(e){return void 0===n[e]&&(n[e]=0),n[e]++},e.each(e(this).serializeArray(),function(){if(i.validate.test(this.name)){for(var n,o=this.name.match(i.key),r=this.value,s=this.name;void 0!==(n=o.pop());)s=s.replace(new RegExp("\\["+n+"\\]$"),""),n.match(i.push)?r=t.build([],t.push_counter(s),r):n.match(i.fixed)?r=t.build([],n,r):n.match(i.named)&&(r=t.build({},n,r));a=e.extend(!0,a,r)}}),a},e.fn.autoHeight=function(t){function a(t){return t.offsetHeight<t.scrollHeight?e(t).css({height:"auto"}).height(t.scrollHeight):e(t)}if(!1!==("boolean"!=typeof t.autogrow||t.autogrow))return this.each(function(){a(this).on("input",function(){a(this)})})},e.fn.removeClassStartingWith=function(e){this.removeClass(function(t,a){return(a.match(new RegExp("\\b"+e+"\\S+","g"))||[]).join(" ")})},e.sanitize=function(t){var a=e(e.parseHTML("<div>"+t+"</div>",null,!1));return a.find("*").each(function(t,a){e.each(a.attributes,function(){var t=this.name,n=this.value;0!=t.indexOf("on")&&0!=n.indexOf("javascript:")||e(a).removeAttr(t)})}),a.html()},e.getUrlParameter=function(e,t){return(RegExp(t+"=(.+?)(&|$)").exec(e)||[,null])[1]},e.fn.recaptcha=function(){var e;this.hasClass("g-recaptcha")&&(e=this.html().length?parseInt(this.find('textarea[name="g-recaptcha-response"]').attr("id").match(/\d+$/),10):grecaptcha.render(this[0],{sitekey:this.attr("data-sitekey")}),Number.isInteger(e)?grecaptcha.reset(e):grecaptcha.reset())},e.fn.captcha=function(){this.hasClass("logic_captcha")&&(this.find('input[name="captcha"]').val(""),this.find(".reload_captcha_base64").trigger("click"))},e.fn.loader=function(t){var a=arguments.length>1&&void 0!==arguments[1]?arguments[1]:"spinner-border";if("show"==t){e(this).parent().find("button").prop("disabled",!0),e(this).find("i").hide();var n=!e(this).is('[class*="btn-outline-"]')||void 0!==e.cookie("theme_toggle")&&"light"!==e.cookie("theme_toggle")?"text-light":"text-dark";e(this).prepend(e.sanitize('<span class="'+a+" "+a+"-sm "+n+'" role="status" aria-hidden="true"></span>'))}"hide"==t&&(e(this).parent().find("button").prop("disabled",!1),e(this).find("i").show(),e(this).find('[role="status"]').remove())},e.fn.addLoader=function(){var t,a=arguments.length>0&&void 0!==arguments[0]?arguments[0]:null;return a={type:"string"==typeof a?a:"spinner-border",class:(null===(t=a)||void 0===t?void 0:t.class)||"loader-absolute"},this.append(e.sanitize('\n            <div class="'.concat(a.class,'">\n                <div class="').concat(a.type,'">\n                    <span class="sr-only">Loading...</span>\n                </div>\n            </div>\n        ')))},e.fn.addAlert=function(t){return t={message:"string"==typeof t?t:t.message,type:t.type||"danger"},this.prepend(e.sanitize('\n            <div class="alert alert-'.concat(t.type,' alert-time" role="alert">\n                <button \n                    type="button" \n                    class="text-dark close" \n                    data-dismiss="alert" \n                    aria-label="Close"\n                >\n                    <span aria-hidden="true">&times;</span>\n                </button>\n                ').concat(t.message,"\n            </div>\n        ")))},e.fn.addToast=function(t){t={title:"string"==typeof t?t:t.title,type:t.type||"success",message:t.message||""};var a=e(e.sanitize('\n            <div>\n                <div \n                    class="toast bg-'.concat(t.type,'"\n                    role="alert" \n                    aria-live="assertive" \n                    aria-atomic="true" \n                    data-delay="20000" \n                >\n                    <div class="toast-header">\n                        <strong class="mr-auto">').concat(t.title,'</strong>\n                        <button \n                            type="button" \n                            class="text-dark ml-2 mb-1 close" \n                            data-dismiss="toast" \n                            aria-label="Close"\n                        >\n                            <span aria-hidden="true">&times;</span>\n                        </button>\n                    </div>             \n                </div>\n            </div>\n        ')));return t.message.length&&a.find(".toast").append(e.sanitize('\n                <div class="toast-body bg-light text-dark">\n                    '.concat(t.message,"\n                </div>\n            "))),this.append(a.html())},e.fn.addError=function(t){t={message:"string"==typeof t?t:t.message,id:t.id||null};var a=e(e.sanitize('\n            <div>\n                <span class="invalid-feedback d-block font-weight-bold">'.concat(t.message,"</span>\n            </div>\n        ")));return null!==t.id&&a.find(".invalid-feedback").attr("id",t.id),this.append(a.html())},e.fn.addMessage=function(t){t={message:"string"==typeof t?t:t.message,id:t.id||null};var a=e(e.sanitize('\n            <div>\n            <span class="valid-feedback d-block font-weight-bold">'.concat(t.message,"</span>\n            </div>\n        ")));return null!==t.id&&a.find(".valid-feedback").attr("id",t.id),this.append(a.html())}}(jQuery),$(document).on("readyAndAjax.n1ebieski/icore/web/scripts/plugins/bootstrap-confirmation@init",function(){$("[data-toggle=confirmation]").each(function(){var e=$(this);e.confirmation({rootSelector:"[data-toggle=confirmation]",copyAttributes:"href data-route data-id",singleton:!0,popout:!0,onConfirm:function(){e.hasClass("submit")&&e.parents("form:first").submit()}})})}),$(document).on("readyAndAjax.n1ebieski/icore/web/scripts/plugins/bootstrap-select/category@init",function(){$("select.select-picker-category").each(function(){var e=$(this);!0!==e.data("loaded")&&(e.selectpicker().on("changed.bs.select",function(){e.next("button").find(".filter-option-inner-inner > small").remove()}).on("shown.bs.select",function(){e.parent().find(".dropdown-menu").find('input[type="search"]').attr("name","search")}).trigger("change"),!0===e.data("abs")&&(e.ajaxSelectPicker({ajax:{data:function(){return{filter:{search:"{{{q}}}",orderby:"real_depth|desc",except:e.data("abs-filter-except")||null,status:1}}}},preprocessData:function(t){var a=[],n=e.data("abs-max-options-length")||t.data.length,i=e.data("abs-default-options")||[];return $.each(i,function(e,t){a.push({value:t.value,text:t.text})}),$.each(t.data,function(t,i){if(t>=n)return!1;a.push({value:e.data("abs-value-attr")?i[e.data("abs-value-attr")]:i.id,text:e.data("abs-text-attr")?i[e.data("abs-text-attr")]:i.name,data:{content:i.ancestors.length?'<small class="p-0 m-0">'+i.ancestors.map(function(e){return e.name}).join(" &raquo; ")+" &raquo; </small>"+i.name:null}})}),a},minLength:3,preserveSelected:"boolean"!=typeof e.data("abs-preserve-selected")||e.data("abs-preserve-selected"),preserveSelectedPosition:e.data("abs-preserve-selected-position")||"before",langCode:e.data("abs-lang-code")||null}),e.trigger("change").data("AjaxBootstrapSelect").list.cache={}),e.parent().addClass("input-group"),e.attr("data-loaded",!0))})}),jQuery(document).on("readyAndAjax.n1ebieski/icore/web/scripts/plugins/bootstrap-select/default@init",function(){$("select.select-picker").each(function(){var e=$(this);!0!==e.data("loaded")&&(e.selectpicker().on("changed.bs.select",function(){e.next("button").find(".filter-option-inner-inner > small").remove()}).on("shown.bs.select",function(){e.parent().find(".dropdown-menu").find('input[type="search"]').attr("name","search")}).trigger("change"),!0===e.data("abs")&&(e.ajaxSelectPicker({ajax:{data:function(){return{filter:{search:"{{{q}}}",except:e.data("abs-filter-except")||null,status:1}}}},preprocessData:function(t){var a=[],n=e.data("abs-max-options-length")||t.data.length,i=e.data("abs-default-options")||[];return $.each(i,function(e,t){a.push({value:t.value,text:t.text})}),$.each(t.data,function(t,i){if(t>=n)return!1;a.push({value:e.data("abs-value-attr")?i[e.data("abs-value-attr")]:i.id,text:e.data("abs-text-attr")?i[e.data("abs-text-attr")]:i.name})}),a},minLength:3,preserveSelected:"boolean"!=typeof e.data("abs-preserve-selected")||e.data("abs-preserve-selected"),preserveSelectedPosition:e.data("abs-preserve-selected-position")||"before",langCode:e.data("abs-lang-code")||null}),e.trigger("change").data("AjaxBootstrapSelect").list.cache={}),e.parent().addClass("input-group"),e.attr("data-loaded",!0))})}),$(document).on("readyAndAjax.n1ebieski/icore/web/scripts/plugins/infinite-scroll@init",function(){var e=$("#infinite-scroll");e.jscroll({debug:!1,autoTrigger:1==e.data("autotrigger"),data:function(){var e=$("#filter").serializeObject().filter||{};if(e.except=$(this).find("[id^=row]").map(function(){return $(this).attr("data-id")}).get(),e.except.length)return{filter:e}},loadingHtml:'<div class="loader"><div class="spinner-border"><span class="sr-only">Loading...</span></div></div>',loadingFunction:function(){$("#is-pagination").first().remove()},padding:0,nextSelector:"a#is-next:last",contentSelector:"#infinite-scroll",pagingSelector:".pagination",callback:function(e){var t=e.split(" ")[0];history.replaceState(null,null,t)}})}),$(document).on("ready.n1ebieski/icore/web/scripts/plugins/jquery-googlemap@init",function(){$("#map, .map").each(function(){var e=$(this);e.length&&(e.data=e.data(),void 0!==e.data.addressMarker&&e.data.addressMarker.length&&(e.googleMap({zoom:parseInt(e.data.zoom),coords:e.data.coords,scrollwheel:!0,type:"ROADMAP"}).addClass(e.data.containerClass),$.each(e.data.addressMarker,function(t,a){e.addMarker({address:a})})))})}),$(document).on("readyAndAjax.n1ebieski/icore/web/scripts/plugins/jquery-lazy@init",function(){$(".lazy").lazy({effect:"fadeIn",effectTime:"fast",threshold:0})}),$(document).on("readyAndAjax.n1ebieski/icore/web/scripts/plugins/magnific-popup@init",function(){var e=$(".lightbox");if(e.length){var t=e.map(function(){return $(this).data("gallery")}).get().filter(function(e,t,a){return t==a.indexOf(e)});$.each(t,function(e,t){$("[data-gallery="+$.escapeSelector(t)+"]").magnificPopup({type:"image",gallery:{enabled:!0}})})}}),$(document).on("ready.n1ebieski/icore/web/scripts/plugins/tagsinput@init",function(){$(".tagsinput").each(function(){var e=$(this);e.tagsInput({placeholder:e.attr("placeholder"),minChars:3,maxChars:e.data("max-chars")||30,limit:e.data("max"),validationPattern:new RegExp("^(?:^[a-zA-ZąćęłńóśźżĄĆĘŁŃÓŚŹŻ0-9à-ü ]+$)$"),unique:!0})})}),$(document).on("ready.n1ebieski/icore/web/scripts/plugins/typeahead@init",function(){var e,t,a;$.when((e=$("#typeahead"),t=e.closest("form"),a=new Bloodhound({remote:{url:e.data("route")+"?filter[search]=%QUERY%",filter:function(e){return $.map(e.data,function(e){return{name:e.name}})},wildcard:"%QUERY%"},datumTokenizer:Bloodhound.tokenizers.whitespace("name"),queryTokenizer:Bloodhound.tokenizers.whitespace}),void e.typeahead({hint:!0,highlight:!0,minLength:3},{limit:5,source:a.ttAdapter(),display:function(e){return $($.parseHTML(e.name)).text()},templates:{suggestion:function(e){var a=$($.parseHTML(e.name)).text(),n=t.attr("action")+"?source="+t.find('[name="source"]').val()+"&search="+a;return $.sanitize('<a href="'+n+'" class="list-group-item py-2 text-truncate">'+a+"</a>")}}}))).then(function(){$("input.tt-input").css("background-color","")})}),$(document).on("readyAndAjax.n1ebieski/icore/web/scripts/view/alerts@init",function(){$(".alert-time").delay(2e4).fadeOut()}),$(document).on("readyAndAjax.n1ebieski/icore/web/scripts/view/bootstrap_tooltips@init",function(){$('[data-toggle="tooltip"]').tooltip()}),$(document).on("readyAndAjax.n1ebieski/icore/web/scripts/view/counter@init",function(){$(".counter").each(function(){var e=$(this);e.name=$.escapeSelector(e.data("name")),e.min=void 0!==e.data("min")&&Number.isInteger(e.data("min"))?e.data("min"):null,e.max=void 0!==e.data("max")&&Number.isInteger(e.data("max"))?e.data("max"):null;var t=function(){var t=[$('[name="'+e.name+'"]'),$('[name="'+e.name+'"]').hasClass("trumbowyg-textarea")?$('[name="'+e.name+'"]').parent().find(".trumbowyg-editor"):null];$.each(t.filter(function(e){return null!=e}),function(){$(this).on("keyup",function(){var t=$(this).attr("contenteditable")?parseFloat($(this).text().length):parseFloat($($.parseHTML($(this).val())).text().length);e.firstchild=e.children(":first"),e.firstchild.text(t),0===t?e.firstchild.removeClass():(e.firstchild.addClass("text-success"),e.firstchild.removeClass("text-danger"),(null!==e.min&&t<e.min||null!==e.max&&t>e.max)&&(e.firstchild.addClass("text-danger"),e.firstchild.removeClass("text-success")))})})};-1!==$('[name="'+e.name+'"]').attr("id").indexOf("trumbowyg")?$("#"+$('[name="'+e.name+'"]').attr("id")).on("tbwinit",function(){return t()}):t()})}),$(document).on("readyAndAjax.n1ebieski/icore/web/scripts/view/custom-file-input@init",function(){$(".custom-file-input").each(function(){$(this).on("change",function(){for(var e=[],t=0;t<$(this)[0].files.length;t++)e.push($(this)[0].files[t].name);$(this).siblings(".custom-file-label").addClass("selected").html(e.join(", "))})})}),$(document).on("ready.n1ebieski/icore/web/scripts/view/navbar@init",function(){var e=$(window).scrollTop(),t=0,a=$(".menu.navbar");!1!==a.data("autohide")&&$(window).on("scroll",function(){if(!$("body").hasClass("modal-open")){if("fixed"===$(".trumbowyg-button-pane").css("position"))return void a.fadeOut();var n=$(window).scrollTop(),i=a.height()+10;e<(t=n)&&e>i?a.fadeOut():a.fadeIn(),e=t}})}),$(document).on("ready.n1ebieski/icore/web/scripts/view/navbar@hashtag",function(){var e=window.location.hash,t=$(".menu.navbar");!1!==t.data("autohide")&&(e.length&&$(window).scrollTop()>t.height()+10&&t.fadeOut())}),$(document).on("click.n1ebieski/icore/web/scripts/view/navbar@toggle",".modal-backdrop, #navbarToggle, #navbar-toggle",function(e){e.preventDefault(),$(".modal-backdrop").length?($(".navbar-collapse").collapse("hide"),$(".modal-backdrop").fadeOut("slow",function(){$(this).remove()}),$("body").removeClass("modal-open")):($(".navbar-collapse").collapse("show"),$('<div class="modal-backdrop show z-900"></div>').appendTo("body").hide().fadeIn(),$("body").addClass("modal-open"))}),$(document).on("click.n1ebieski/icore/web/scripts/view/policy@agree","#policy #agree",function(e){e.preventDefault(),$("#policy").remove(),$.cookie("policy_agree",1,{path:"/",expires:365})}),$(document).on("scroll.n1ebieski/icore/web/scripts/view/scroll_to_top@init",function(){$(this).scrollTop()>100?$(".scroll-to-top").fadeIn():$(".scroll-to-top").fadeOut()}),$(document).on("click.n1ebieski/icore/web/scripts/view/scroll_to_top@scroll","a.scroll-to-top",function(e){$("html, body").stop().animate({scrollTop:0},1e3,"easeInOutExpo"),e.preventDefault()}),$(document).on("click.n1ebieski/icore/web/scripts/view/search@toggle",".search-toggler",function(e){e.preventDefault(),window.innerWidth>=768?$("#pagesToggle, #pages-toggle").fadeToggle(0):($("#navbarLogo, #navbar-logo").fadeToggle(0),$("#navbarToggle, #navbar-toggle").fadeToggle(0)),$("#searchForm, #search-form").fadeToggle(0),$(".search-toggler").find("i").toggleClass("fa-search fa-times")}),$(document).on("ready.n1ebieski/icore/web/scripts/view/search@disable",function(){var e=$("form#searchForm, form#search-form");e.btn=e.find("button"),e.find('input[name="search"]').keyup(function(t){$(this).val().trim().length>=3?e.btn.prop("disabled",!1):e.btn.prop("disabled",!0)})}),$(document).on("readyAndAjax.n1ebieski/icore/web/scripts/view/search@enter",function(){var e=$("form#searchForm, form#search-form");e.btn=e.find("button"),e.find('input[name="search"]').on("keypress",function(t){if(13==t.which&&!1===e.btn.prop("disabled"))return $("form#searchForm, form#search-form").trigger("submit"),!1})}),$(document).on("readyAndAjax.n1ebieski/icore/web/scripts/view/textarea@init",function(){$("textarea").each(function(){$(this).autoHeight({autogrow:$(this).data("autogrow")})})}),$(document).on("click.n1ebieski/icore/web/scripts/view/theme@toggle","div#themeToggle button, div#theme-toggle button",function(e){e.preventDefault();var t=$(this);t.hasClass("btn-light")&&$.cookie("theme_toggle","light",{path:"/",expires:365}),t.hasClass("btn-dark")&&$.cookie("theme_toggle","dark",{path:"/",expires:365}),window.location.reload()}),$(document).on("readyAndAjax.n1ebieski/icore/web/scripts/view/toasts@init",function(){$(".toast").toast("show"),$(".toast").on("hidden.bs.toast",function(){$(this).remove()})});
