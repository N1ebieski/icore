function categorySelect(){return $("#categoryOptions .form-group").map(function(){return"#"+$(this).attr("id")}).get()}function ajaxCreateComment(e){let t=e.closest("[id^=comment]");$.ajax({url:e.data("route"),method:"get",beforeSend:function(){e.prop("disabled",!0),t.append($.getLoader("spinner-border","loader"))},complete:function(){e.prop("disabled",!1),t.find(".loader").remove(),t.find(".captcha").recaptcha()},success:function(e){t.children("div").append($.sanitize(e.view))},error:function(e){e.responseJSON.message&&t.children("div").prepend($.getAlert("danger",e.responseJSON.message))}})}jQuery(document).ready(function(){$(document).trigger("readyAndAjax")}),jQuery(document).ajaxComplete(function(){$(document).trigger("readyAndAjax")}),jQuery(document).on("readyAndAjax",function(){$("form").find("input, select").keypress(function(e){if(13==e.which)return e.preventDefault(),!1})}),jQuery(document).on("click","#searchCategory .btn",function(e){e.preventDefault();let t=$("#searchCategory");t.url=t.attr("data-route"),t.btn=t.find(".btn"),t.input=t.find("input"),$.ajax({url:t.url+"?name="+t.input.val(),method:"get",dataType:"json",beforeSend:function(){t.btn.prop("disabled",!0),$("#searchCategoryOptions").empty(),t.append($.getLoader("spinner-border")),t.find(".invalid-feedback").remove(),t.input.removeClass("is-valid"),t.input.removeClass("is-invalid")},complete:function(){t.btn.prop("disabled",!1),t.find("div.loader-absolute").remove()},success:function(e){let n=$(e.view).find(categorySelect().join(",")).remove().end();t.find("#searchCategoryOptions").html($.sanitize(n.html()))},error:function(e){var n=e.responseJSON;$.each(n.errors,function(e,n){t.input.addClass("is-invalid"),t.input.parent().after($.getError(e,n))})}})}),jQuery(document).on("change",".categoryOption",function(){let e=$("#searchCategory");e.max=e.attr("data-max");let t=$(this).closest(".form-group");1==$(this).prop("checked")?t.appendTo("#categoryOptions"):t.remove(),$.isNumeric(e.max)&&(e.is(":visible")&&categorySelect().length>=e.max&&e.fadeOut(),!e.is(":visible")&&categorySelect().length<e.max&&e.fadeIn())}),jQuery(document).on("readyAndAjax",function(){$("#searchCategory input").keypress(function(e){if(13==e.which)return $("#searchCategory .btn").trigger("click"),!1})}),jQuery(document).on("click","a.createComment, a.create-comment",function(e){e.preventDefault();let t=$(this).closest("[id^=comment]").find("form#createComment, form#create-comment");t.length>0?t.fadeToggle():ajaxCreateComment($(this))}),jQuery(document).on("click","a.editComment, a.edit-comment",function(e){e.preventDefault();let t=$(this),n=t.closest("[id^=comment]");$.ajax({url:t.data("route"),method:"get",beforeSend:function(){n.children("div").hide(),n.append($.getLoader("spinner-border","loader"))},complete:function(){n.find(".loader").remove()},success:function(e){n.append($.sanitize(e.view))},error:function(e){n.children("div").show(),e.responseJSON.message&&n.children("div").prepend($.getAlert("danger",e.responseJSON.message))}})}),jQuery(document).on("click","button.editCommentCancel, button.edit-comment-cancel",function(e){e.preventDefault();let t=$(this).closest("[id^=comment]");t.children("div").show(),t.find("form#editComment, form#edit-comment").remove()}),function(e){jQuery(document).on("change","#filterCommentOrderBy, #filter-orderby-comment",function(t){t.preventDefault();let n=e("#filter");n.href=n.data("route")+"?"+n.serialize(),function(t,n){e.ajax({url:n,method:"get",dataType:"html",beforeSend:function(){e("#filterContent, #filter-content").find(".btn").prop("disabled",!0),e("#filterOrderBy, #filter-orderby").prop("disabled",!0),e("#filterPaginate, #filter-paginate").prop("disabled",!0),t.children("div").append(e.getLoader("spinner-border")),e("#filterModal, #filter-modal").modal("hide")},complete:function(){t.find(".loader-absolute").remove(),e("div#comment").find(".captcha").recaptcha()},success:function(t){e("#filterContent, #filter-content").html(e.sanitize(e(t).find("#filterContent, #filter-content").html())),document.title=document.title.replace(/:\s(\d+)/,": 1"),history.replaceState(null,null,n)}})}(n,n.href)})}(jQuery),jQuery(document).on("click","a.rateComment, a.rate-comment",function(e){e.preventDefault();let t=$(this),n=t.closest("[id^=comment]").find("span.rating");$.ajax({url:t.data("route"),method:"get",complete:function(){n.addClass("font-weight-bold")},success:function(e){n.text(e.sum_rating)}})}),jQuery(document).on("click",".storeComment, .store-comment",function(e){e.preventDefault();let t=$(this),n=t.closest("form");n.btn=n.find(".btn"),n.input=n.find(".form-control"),jQuery.ajax({url:n.data("route"),method:"post",data:n.serialize(),dataType:"json",beforeSend:function(){t.getLoader("show"),$(".invalid-feedback").remove(),n.input.removeClass("is-valid"),n.input.removeClass("is-invalid")},complete:function(){t.getLoader("hide"),n.input.addClass("is-valid"),n.find(".captcha").recaptcha(),n.find(".captcha").captcha()},success:function(e){if(e.view){n.closest("[id^=comment]").after($.sanitize(e.view));let t=n.closest("[id^=comment]").next("div");t.addClass("alert-primary font-italic border-bottom"),setTimeout(function(){t.removeClassStartingWith("alert-")},5e3)}e.success&&n.before($.getAlert("success",e.success)),0!=n.find("#parent_id").val()?n.remove():n.find("#content").val("")},error:function(e){e.responseJSON.errors?$.each(e.responseJSON.errors,function(e,t){n.find("#"+$.escapeSelector(e)).addClass("is-invalid"),n.find("#"+$.escapeSelector(e)).closest(".form-group").append($.getError(e,t))}):e.responseJSON.message&&n.prepend($.getAlert("danger",e.responseJSON.message))}})}),jQuery(document).on("click","a.takeComment, a.take-comment",function(e){e.preventDefault();let t=$(this),n=t.closest("[id^=row]"),a=t.closest("div");$.ajax({url:t.data("route"),method:"post",data:{filter:{except:n.children("[id^=row]").map(function(){return $(this).attr("data-id")}).get(),orderby:t.closest("#filterContent, #filter-content").find("#filterCommentOrderBy, #filter-orderby-comment").val()}},beforeSend:function(){t.hide(),a.append($.getLoader("spinner-border","loader"))},complete:function(){a.find(".loader").remove()},success:function(e){n.append($.sanitize(e.view))}})}),jQuery(document).on("click",".updateComment, .update-comment",function(e){e.preventDefault();let t=$(this),n=t.closest("form");n.btn=n.find(".btn"),n.input=n.find(".form-control"),jQuery.ajax({url:n.data("route"),method:"put",data:n.serialize(),dataType:"json",beforeSend:function(){t.getLoader("show"),$(".invalid-feedback").remove(),n.input.removeClass("is-valid"),n.input.removeClass("is-invalid")},complete:function(){t.getLoader("hide"),n.input.addClass("is-valid")},success:function(e){let t=n.closest("[id^=comment]");t.html($.sanitize($(e.view).html())),t.addClass("alert-primary"),setTimeout(function(){t.removeClassStartingWith("alert-")},5e3)},error:function(e){e.responseJSON.errors?$.each(e.responseJSON.errors,function(e,t){n.find("#"+$.escapeSelector(e)).addClass("is-invalid"),n.find("#"+$.escapeSelector(e)).closest(".form-group").append($.getError(e,t))}):e.responseJSON.message&&n.prepend($.sanitize($.getAlert("danger",e.responseJSON.message)))}})}),jQuery(document).on("click",".destroy",function(e){e.preventDefault();let t=$(this),n=$("#row"+t.data("id"));jQuery.ajax({url:t.data("route"),method:"delete",beforeSend:function(){n.find(".responsive-btn-group").addClass("disabled"),n.find('[data-btn-ok-class*="destroy"]').getLoader("show")},complete:function(){n.find('[data-btn-ok-class*="destroy"]').getLoader("hide")},success:function(e){n.fadeOut("slow")}})}),function(e){function t(t,n){e.ajax({url:n,method:"get",dataType:"html",beforeSend:function(){e("#filterContent, #filter-content").find(".btn").prop("disabled",!0),e("#filterOrderBy, #filter-orderby").prop("disabled",!0),e("#filterPaginate, #filter-paginate").prop("disabled",!0),t.children("div").append(e.getLoader("spinner-border")),e("#filterModal, #filter-modal").modal("hide")},complete:function(){t.find(".loader-absolute").remove()},success:function(t){e(".modal-backdrop").remove(),e("body").removeClass("modal-open").removeAttr("style"),e("#filterContent, #filter-content").html(e.sanitize(e(t).find("#filterContent, #filter-content").html())),document.title=document.title.replace(/:\s(\d+)/,": 1"),history.replaceState(null,null,n)}})}jQuery(document).on("change","#filterOrderBy, #filter-orderby",function(n){n.preventDefault();let a=e("#filter");a.href=a.data("route")+"?"+a.serialize(),t(a,a.href)}),jQuery(document).on("click","#filterFilter, #filter-filter",function(n){n.preventDefault();let a=e("#filter");a.href=a.data("route")+"?"+a.serialize(),e("#filter").valid()&&t(a,a.href)}),jQuery(document).on("click","a.filterOption, a.filter-option",function(n){n.preventDefault();let a=e("#filter");a.href=a.data("route")+"?"+a.find("[name!="+e.escapeSelector(e(this).data("name"))+"]").serialize(),t(a,a.href)}),jQuery(document).on("change","#filterPaginate, #filter-paginate",function(n){n.preventDefault();let a=e("#filter");a.href=a.data("route")+"?"+a.serialize(),t(a,a.href)})}(jQuery),jQuery(document).on("click",".storeNewsletter, .store-newsletter",function(e){e.preventDefault();let t=$(this),n=t.parents("form");n.btn=n.find(".btn"),n.group=n.find(".form-group"),n.input=n.find(".form-control, .custom-control-input"),jQuery.ajax({url:n.data("route"),method:"post",data:n.serialize(),dataType:"json",beforeSend:function(){t.getLoader("show"),$(".invalid-feedback").remove(),$(".valid-feedback").remove(),n.input.removeClass("is-valid"),n.input.removeClass("is-invalid")},complete:function(){t.getLoader("hide"),n.input.addClass("is-valid")},success:function(e){e.success&&(n.find('[name="email"]').val(""),n.find('[name="email"]').closest(".form-group").append($.getMessage(e.success)))},error:function(e){e.responseJSON.errors&&$.each(e.responseJSON.errors,function(e,t){n.find('[name="'+e+'"]').addClass("is-invalid"),n.find('[name="'+e+'"]').closest(".form-group").append($.getError(e,t))})}})}),jQuery(document).on("click","a.createReport, a.create-report",function(e){e.preventDefault();let t=$(this),n={body:$(t.attr("data-target")).find(".modal-body"),content:$(t.attr("data-target")).find(".modal-content")};n.body.empty(),jQuery.ajax({url:t.data("route"),method:"get",beforeSend:function(){n.body.html($.getLoader("spinner-grow"))},complete:function(){n.content.find(".loader-absolute").remove()},success:function(e){n.body.html($.sanitize(e.view))}})}),jQuery(document).on("click",".storeReport, .store-report",function(e){e.preventDefault();let t=$(this),n=t.closest("form");n.btn=n.find(".btn"),n.input=n.find(".form-control");let a={body:n.closest(".modal-body")};$.ajax({url:n.data("route"),method:"post",data:n.serialize(),dataType:"json",beforeSend:function(){t.getLoader("show"),$(".invalid-feedback").remove(),n.input.removeClass("is-valid"),n.input.removeClass("is-invalid")},complete:function(){t.getLoader("hide"),n.input.addClass("is-valid")},success:function(e){a.body.html($.getAlert("success",e.success))},error:function(e){let t=e.responseJSON;$.each(t.errors,function(e,t){n.find("#"+$.escapeSelector(e)).addClass("is-invalid"),n.find("#"+$.escapeSelector(e)).after($.getError(e,t))})}})}),function(e){e.fn.serializeObject=function(){var t=this,n={},a={},o={validate:/^[a-zA-Z][a-zA-Z0-9_]*(?:\[(?:\d*|[a-zA-Z0-9_]+)\])*$/,key:/[a-zA-Z0-9_]+|(?=\[\])/g,push:/^$/,fixed:/^\d+$/,named:/^[a-zA-Z0-9_]+$/};return this.build=function(e,t,n){return e[t]=n,e},this.push_counter=function(e){return void 0===a[e]&&(a[e]=0),a[e]++},e.each(e(this).serializeArray(),function(){if(o.validate.test(this.name)){for(var a,r=this.name.match(o.key),i=this.value,s=this.name;void 0!==(a=r.pop());)s=s.replace(new RegExp("\\["+a+"\\]$"),""),a.match(o.push)?i=t.build([],t.push_counter(s),i):a.match(o.fixed)?i=t.build([],a,i):a.match(o.named)&&(i=t.build({},a,i));n=e.extend(!0,n,i)}}),n},e.fn.autoHeight=function(t){function n(t){return t.offsetHeight<t.scrollHeight?e(t).css({height:"auto"}).height(t.scrollHeight):e(t)}if(!1!==("boolean"!=typeof t.autogrow||t.autogrow))return this.each(function(){n(this).on("input",function(){n(this)})})},e.fn.removeClassStartingWith=function(e){this.removeClass(function(t,n){return(n.match(new RegExp("\\b"+e+"\\S+","g"))||[]).join(" ")})},e.sanitize=function(t){let n=e(e.parseHTML("<div>"+t+"</div>",null,!1));return n.find("*").each(function(t,n){e.each(n.attributes,function(){let t=this.name,a=this.value;0!=t.indexOf("on")&&0!=a.indexOf("javascript:")||e(n).removeAttr(t)})}),n.html()},e.getUrlParameter=function(e,t){return(RegExp(t+"=(.+?)(&|$)").exec(e)||[,null])[1]},e.fn.recaptcha=function(){var e;this.hasClass("g-recaptcha")&&(e=this.html().length?parseInt(this.find('textarea[name="g-recaptcha-response"]').attr("id").match(/\d+$/),10):grecaptcha.render(this[0],{sitekey:this.attr("data-sitekey")}),Number.isInteger(e)?grecaptcha.reset(e):grecaptcha.reset())},e.fn.captcha=function(){this.hasClass("logic_captcha")&&(this.find('input[name="captcha"]').val(""),this.find(".reload_captcha_base64").trigger("click"))},e.fn.getLoader=function(t,n="spinner-border"){if("show"==t){e(this).parent().find("button").prop("disabled",!0),e(this).find("i").hide();let t=!e(this).is('[class*="btn-outline-"]')||void 0!==e.cookie("theme_toggle")&&"light"!==e.cookie("theme_toggle")?"text-light":"text-dark";e(this).prepend(e.sanitize('<span class="'+n+" "+n+"-sm "+t+'" role="status" aria-hidden="true"></span>'))}"hide"==t&&(e(this).parent().find("button").prop("disabled",!1),e(this).find("i").show(),e(this).find('[role="status"]').remove())},e.getLoader=function(t="spinner-border",n="loader-absolute"){return e.sanitize('<div class="'+n+'"><div class="'+t+'"><span class="sr-only">Loading...</span></div></div>')},e.getAlert=function(t,n){return e.sanitize('<div class="alert alert-'+t+' alert-time" role="alert">'+n+"</div>")},e.getError=function(t,n){return e.sanitize('<span class="invalid-feedback d-block font-weight-bold" id="error-'+t+'">'+n+"</span>")},e.getMessage=function(t){return e.sanitize('<span class="valid-feedback d-block font-weight-bold">'+t+"</span>")}}(jQuery),jQuery(document).on("readyAndAjax",function(){$("[data-toggle=confirmation]").each(function(){let e=$(this);e.confirmation({rootSelector:"[data-toggle=confirmation]",copyAttributes:"href data-route data-id",singleton:!0,popout:!0,onConfirm:function(){e.hasClass("submit")&&e.parents("form:first").submit()}})})}),jQuery(document).on("readyAndAjax",function(){$(".select-picker-category").each(function(){let e=$(this);!0!==e.data("loaded")&&(e.selectpicker().on("changed.bs.select",function(){e.next("button").find(".filter-option-inner-inner > small").remove()}).on("shown.bs.select",function(){e.parent().find(".dropdown-menu").find('input[type="search"]').attr("name","search")}).trigger("change"),!0===e.data("abs")&&(e.ajaxSelectPicker({ajax:{data:function(){return{filter:{search:"{{{q}}}",orderby:"real_depth|desc",except:e.data("abs-filter-except")||null,status:1}}}},preprocessData:function(t){let n=[],a=e.data("abs-max-options-length")||t.data.length,o=e.data("abs-default-options")||[];return $.each(o,function(e,t){n.push({value:t.value,text:t.text})}),$.each(t.data,function(t,o){if(t>=a)return!1;n.push({value:e.data("abs-value-attr")?o[e.data("abs-value-attr")]:o.id,text:e.data("abs-text-attr")?o[e.data("abs-text-attr")]:o.name,data:{content:o.ancestors.length?'<small class="p-0 m-0">'+o.ancestors.map(e=>e.name).join(" &raquo; ")+" &raquo; </small>"+o.name:null}})}),n},minLength:3,preserveSelected:"boolean"!=typeof e.data("abs-preserve-selected")||e.data("abs-preserve-selected"),preserveSelectedPosition:e.data("abs-preserve-selected-position")||"before",langCode:e.data("abs-lang-code")||null}),e.trigger("change").data("AjaxBootstrapSelect").list.cache={}),e.parent().addClass("input-group"),e.attr("data-loaded",!0))})}),jQuery(document).on("readyAndAjax",function(){$(".select-picker").each(function(){let e=$(this);!0!==e.data("loaded")&&(e.selectpicker().on("changed.bs.select",function(){e.next("button").find(".filter-option-inner-inner > small").remove()}).on("shown.bs.select",function(){e.parent().find(".dropdown-menu").find('input[type="search"]').attr("name","search")}).trigger("change"),!0===e.data("abs")&&(e.ajaxSelectPicker({ajax:{data:function(){return{filter:{search:"{{{q}}}",except:e.data("abs-filter-except")||null,status:1}}}},preprocessData:function(t){let n=[],a=e.data("abs-max-options-length")||t.data.length,o=e.data("abs-default-options")||[];return $.each(o,function(e,t){n.push({value:t.value,text:t.text})}),$.each(t.data,function(t,o){if(t>=a)return!1;n.push({value:e.data("abs-value-attr")?o[e.data("abs-value-attr")]:o.id,text:e.data("abs-text-attr")?o[e.data("abs-text-attr")]:o.name})}),n},minLength:3,preserveSelected:"boolean"!=typeof e.data("abs-preserve-selected")||e.data("abs-preserve-selected"),preserveSelectedPosition:e.data("abs-preserve-selected-position")||"before",langCode:e.data("abs-lang-code")||null}),e.trigger("change").data("AjaxBootstrapSelect").list.cache={}),e.parent().addClass("input-group"),e.attr("data-loaded",!0))})}),jQuery(document).on("readyAndAjax",function(){let e=$("#infinite-scroll");e.jscroll({debug:!1,autoTrigger:1==e.data("autotrigger"),data:function(){let e=$("#filter").serializeObject().filter||{};if(e.except=$(this).find("[id^=row]").map(function(){return $(this).attr("data-id")}).get(),Object.keys(e).length)return{filter:e}},loadingHtml:$.getLoader("spinner-border","loader"),loadingFunction:function(){$("#is-pagination").first().remove()},padding:0,nextSelector:"a#is-next:last",contentSelector:"#infinite-scroll",pagingSelector:".pagination",callback:function(e){let t=e.split(" ")[0];history.replaceState(null,null,t)}})}),jQuery(document).ready(function(){$("#map, .map").each(function(){let e=$(this);e.length&&(e.data=e.data(),void 0!==e.data.addressMarker&&e.data.addressMarker.length&&(e.googleMap({zoom:parseInt(e.data.zoom),coords:e.data.coords,scrollwheel:!0,type:"ROADMAP"}).addClass(e.data.containerClass),$.each(e.data.addressMarker,function(t,n){e.addMarker({address:n})})))})}),jQuery(document).on("readyAndAjax",function(){$(".lazy").lazy({effect:"fadeIn",effectTime:"fast",threshold:0})}),$(document).on("readyAndAjax",function(){let e=$(".lightbox");if(e.length){let t=e.map(function(){return $(this).data("gallery")}).get().filter(function(e,t,n){return t==n.indexOf(e)});$.each(t,function(e,t){$("[data-gallery="+$.escapeSelector(t)+"]").magnificPopup({type:"image",gallery:{enabled:!0}})})}}),jQuery(document).ready(function(){$(".tagsinput").each(function(){let e=$(this);e.tagsInput({placeholder:e.attr("placeholder"),minChars:3,maxChars:e.data("max-chars")||30,limit:e.data("max"),validationPattern:new RegExp("^(?:^[a-zA-ZąćęłńóśźżĄĆĘŁŃÓŚŹŻ0-9à-ü ]+$)$"),unique:!0})})}),function(e){jQuery(document).ready(function(){e.when(function(){let t=e("#typeahead"),n=t.closest("form"),a=new Bloodhound({remote:{url:t.data("route")+"?filter[search]=%QUERY%",filter:function(t){return e.map(t.data,function(e){return{name:e.name}})},wildcard:"%QUERY%"},datumTokenizer:Bloodhound.tokenizers.whitespace("name"),queryTokenizer:Bloodhound.tokenizers.whitespace});t.typeahead({hint:!0,highlight:!0,minLength:3},{limit:5,source:a.ttAdapter(),display:function(t){return e(e.parseHTML(t.name)).text()},templates:{suggestion:function(t){let a=e(e.parseHTML(t.name)).text(),o=n.attr("action")+"?source="+n.find('[name="source"]').val()+"&search="+a;return e.sanitize('<a href="'+o+'" class="list-group-item py-2 text-truncate">'+a+"</a>")}}})}()).then(function(){e("input.tt-input").css("background-color","")})})}(jQuery),jQuery(document).on("readyAndAjax",function(){$(".alert-time").delay(2e4).fadeOut()}),jQuery(document).on("readyAndAjax",function(){$('[data-toggle="tooltip"]').tooltip()}),jQuery(document).on("readyAndAjax",function(){$(".counter").each(function(){let e=$(this);e.name=$.escapeSelector(e.data("name")),e.min=void 0!==e.data("min")&&Number.isInteger(e.data("min"))?e.data("min"):null,e.max=void 0!==e.data("max")&&Number.isInteger(e.data("max"))?e.data("max"):null;let t=function(){let t=[$('[name="'+e.name+'"]'),$('[name="'+e.name+'"]').hasClass("trumbowyg-textarea")?$('[name="'+e.name+'"]').parent().find(".trumbowyg-editor"):null];$.each(t.filter(e=>null!=e),function(){$(this).keyup(function(){let t=$(this).attr("contenteditable")?parseFloat($(this).text().length):parseFloat($($.parseHTML($(this).val())).text().length);e.firstchild=e.children(":first"),e.firstchild.text(t),0===t?e.firstchild.removeClass():(e.firstchild.addClass("text-success"),e.firstchild.removeClass("text-danger"),(null!==e.min&&t<e.min||null!==e.max&&t>e.max)&&(e.firstchild.addClass("text-danger"),e.firstchild.removeClass("text-success")))})})};-1!==$('[name="'+e.name+'"]').attr("id").indexOf("trumbowyg")?$("#"+$('[name="'+e.name+'"]').attr("id")).on("tbwinit",()=>t()):t()})}),jQuery(document).on("readyAndAjax",function(){$(".custom-file-input").each(function(){$(this).on("change",function(){for(var e=[],t=0;t<$(this)[0].files.length;t++)e.push($(this)[0].files[t].name);$(this).siblings(".custom-file-label").addClass("selected").html(e.join(", "))})})}),jQuery(document).ready(function(){let e=$(window).scrollTop(),t=0,n=$(".menu.navbar");!1!==n.data("autohide")&&$(window).scroll(function(){if(!$("body").hasClass("modal-open")){var a=$(window).scrollTop(),o=n.height()+10;e<(t=a)&&e>o?n.fadeOut():n.fadeIn(),e=t}})}),jQuery(document).ready(function(){let e=window.location.hash,t=$(".menu.navbar");if(!1!==t.data("autohide")&&e.length){$(window).scrollTop()>t.height()+10&&t.fadeOut()}}),jQuery(document).on("click",".modal-backdrop, #navbarToggle, #navbar-toggle",function(e){e.preventDefault(),$(".modal-backdrop").length?($(".navbar-collapse").collapse("hide"),$(".modal-backdrop").fadeOut("slow",function(){$(this).remove()}),$("body").removeClass("modal-open")):($(".navbar-collapse").collapse("show"),$('<div class="modal-backdrop show z-900"></div>').appendTo("body").hide().fadeIn(),$("body").addClass("modal-open"))}),jQuery(document).on("click","#policy #agree",function(e){e.preventDefault(),$("#policy").remove(),$.cookie("policy_agree",1,{path:"/",expires:365})}),$(document).on("scroll",function(){$(this).scrollTop()>100?$(".scroll-to-top").fadeIn():$(".scroll-to-top").fadeOut()}),$(document).on("click","a.scroll-to-top",function(e){$("html, body").stop().animate({scrollTop:0},1e3,"easeInOutExpo"),e.preventDefault()}),$(document).on("click",".search-toggler",function(e){e.preventDefault(),window.innerWidth>=768?$("#pagesToggle, #pages-toggle").fadeToggle(0):($("#navbarLogo, #navbar-logo").fadeToggle(0),$("#navbarToggle, #navbar-toggle").fadeToggle(0)),$("#searchForm, #search-form").fadeToggle(0),$(".search-toggler").find("i").toggleClass("fa-search fa-times")}),$(document).ready(function(){let e=$("form#searchForm, form#search-form");e.btn=e.find("button"),e.find('input[name="search"]').keyup(function(t){$(this).val().trim().length>=3?e.btn.prop("disabled",!1):e.btn.prop("disabled",!0)})}),jQuery(document).on("readyAndAjax",function(){let e=$("form#searchForm, form#search-form");e.btn=e.find("button"),e.find('input[name="search"]').keypress(function(t){if(13==t.which&&!1===e.btn.prop("disabled"))return $("form#searchForm, form#search-form").submit(),!1})}),jQuery(document).on("readyAndAjax",function(){$("textarea").each(function(){$(this).autoHeight({autogrow:$(this).data("autogrow")})})}),jQuery(document).on("click","div#themeToggle button, div#theme-toggle button",function(e){e.preventDefault();let t=$(this);t.hasClass("btn-light")&&$.cookie("theme_toggle","light",{path:"/",expires:365}),t.hasClass("btn-dark")&&$.cookie("theme_toggle","dark",{path:"/",expires:365}),window.location.reload()}),jQuery(document).on("readyAndAjax",function(){$(".toast").toast("show"),$(".toast").on("hidden.bs.toast",function(){$(this).remove()})});
