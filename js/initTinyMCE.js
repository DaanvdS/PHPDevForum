tinyMCE.PluginManager.add('stylebuttons', function(editor, url) {
			  ['p', 'code', 'h1'].forEach(function(name){
			   editor.addButton("style-" + name, {
				   tooltip: "Toggle " + name,
					 text: name.toUpperCase(),
					 onClick: function() { editor.execCommand('mceToggleFormat', false, name); },
					 onPostRender: function() {
						 var self = this, setup = function() {
							 editor.formatter.formatChanged(name, function(state) {
								 self.active(state);
							 });
						 };
						 editor.formatter ? setup() : editor.on('init', setup);
					 }
				 })
			  });
			});
			tinymce.init({
				selector: "textarea",
				content_css : "styles/tinymce.css",
				menubar : false,
				toolbar: [
					"bold italic strikethrough | undo redo | style-p style-h1 style-code | bullist numlist | link image "
				],
				statusbar : false,
				plugins: "stylebuttons link image"
			});