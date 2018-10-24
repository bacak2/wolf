$(function() {

	var expanded = [];

	function add_category_id_to_expanded(category_id) {
		if (jQuery.inArray(category_id, expanded) == -1) {
			expanded.push(category_id);
			return true;
		}
		else return false;
	}

	function remove_category_id_from_expanded(category_id) {
		var idx = jQuery.inArray(category_id, expanded);
		if (idx != -1) {
			expanded.splice(idx, 1);
			return true;
		}
		else return false;
	}

	$(window).ready(function() {
		$(".expander").each(function(i, obj) {
                    if($(this).parent().parent().parent().hasClass('expanded')){
                        var category_id = $(this).attr('data-id');
                        if(category_id != undefined){
                            add_category_id_to_expanded(category_id);
                        }
                    }
		});
	});


	$("#categories-expand-all").on("click", function(e){
		e.preventDefault();
		$(".lista").expandAll();
	});

	$("#categories-collapse-all").on("click", function(e){
		e.preventDefault();
		$(".lista").collapseAll();
	});

        $("#categories-expand-all-left").on("click", function(e){
		e.preventDefault();
		$("#left-menu-categories").expandAll();
	});

	$("#categories-collapse-all-left").on("click", function(e){
		e.preventDefault();
		$("#left-menu-categories").collapseAll();
	});

	$(".expander").on("click", function(e){
            var parent = $(this).parent();
            var td_parent = parent.parent();
            var category_id = $(this).attr('data-id');
            if(td_parent.parent().hasClass('expanded')){
                td_parent.parent().collapse();
            }else if(td_parent.parent().hasClass('collapsed')){
                td_parent.parent().expand();
            }

            if (td_parent.parent().hasClass('expanded') && add_category_id_to_expanded(category_id)) {
            }
            else if (td_parent.parent().hasClass('collapsed')) {
                    remove_category_id_from_expanded(category_id);
            }
            $.cookie(cookieprefix, expanded.toString());
            return false;
	});

        function childrenOf(node) {
            return $(node).siblings("tr.child-of-" + node[0].id);
        };
          // Recursively hide all node's children in a tree
          $.fn.collapse = function() {
                $(this).removeClass("expanded").addClass("collapsed");
                childrenOf($(this)).each(function() {
                if(!$(this).hasClass("collapsed")) {
                    $(this).collapse();
                }
                $(this).addClass('hidden-class');
                });
            return this;
            };

          // Recursively show all node's children in a tree
          $.fn.expand = function() {
            $(this).removeClass("collapsed").addClass("expanded");

            childrenOf($(this)).each(function() {
              initialize($(this));
              if($(this).is(".expanded.parent")) {
                $(this).expand();
              }

              $(this).removeClass('hidden-class');
            });

            return this;
          };

          $.fn.expandAll = function() {
		$(this).find("tr").removeClass("collapsed").addClass("expanded").each(function(){
			$(this).expand();
                        category_id = $(this).attr('this-id');
                        add_category_id_to_expanded(category_id)
		});
                $.cookie(cookieprefix, expanded.toString());
	};

          $.fn.collapseAll = function() {
		$(this).find("tr").removeClass("expanded").addClass("collapsed").each(function(){
			$(this).collapse();
                        category_id = $(this).attr('this-id');
                        remove_category_id_from_expanded(category_id);
		});
                $.cookie(cookieprefix, expanded.toString());
	};

          function initialize(node) {
            if(!node.hasClass("initialized")) {
            node.addClass("initialized");
            var childNodes = childrenOf(node);
            if(!node.hasClass("parent") && childNodes.length > 0) {
                node.addClass("parent");
            }

            if(node.hasClass("parent")) {
                // Check for a class set explicitly by the user, otherwise set the default class
                if(!(node.hasClass("expanded") || node.hasClass("collapsed"))) {
                    node.addClass('collapsed');
                }
                if(node.hasClass("expanded")) {
                    node.expand();
                }
            }
        }
    };
});