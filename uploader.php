<article class="goldback">

	<div class="articlehead">
		<div class="title">
			<h4>Javascript Multiple File Upload Without A Plug-In</h4>
		</div>
		<div class="timestamp">
			06/30/15
		</div>
	</div>

	<p>
		I am currently adding functionality to an existing web application that relies heavily on a stew of JavaScript plugins to do the most mundane of
		tasks. That's a great way to save time, but eventually one ends up with a mish-mash of libraries that no one person can ever understand, or ever
		hope to maintain. I think it is indicative of a dumbing down of skill-sets, especially when libraries and plug-ins are used where a few lines of
		code would suffice. I like to think the developers that come after me will be happy that they don't have to know anything other than JavaScript.
		
		This code is based on code from <a href="http://www.matlus.com/html5-file-upload-with-progress/">www.matlus.com</a>, and will only work on HTML5
		compliant browsers.

		Unless of course you have no idea how to use JavaScript. By all means, use a plug-in.

		<br>
		<br>
		To start, we create a basic HTML template <span id="htmltemplate" class="articlelink">(Or use this one)</span>, and add some basic fields.
	</p>

	<xmp>
		<!DOCTYPE html>
		<!-- Basic HTML5 template from extant.digital -->
		<html>
			<head>
				<title>Basic Template</title>

				<style>
				</style>

				<script></script>
			</head>
			<body>

				<input type="file" name="fileToUpload" id="fileToUpload" onchange="fileSelected();" multiple="true"/>
				<div class="files"></div>
				<button onclick="uploadFile()">
					Upload
				</button>

				<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>

			</body>
		</html>
	</xmp>

	<p>
		I am using html onchange listeners to eliminate a couple of listeners, and adding simple style to make sure that the spans take up space on the page.
	</p>

	<xmp>
		<style>
			.fileObject div span{
			    display: inline-block;
			    height: 20px;
			    min-width: 100px;
			}
		</style>
	</xmp>

	<p>
		Now for the JavaScript. Since we are already using the built-in file selection capability of HTML5, all we really need to do is add some additional functionality to add
		a view for each of the files we want to upload. When the user selects some files, the <strong>fileSelected</strong> function is fired. It in turn calls <strong>fileToUpload</strong>, 
		passing it a single file.
		<br>
		The 'file' is a File Object <a href="https://developer.mozilla.org/en-US/docs/Web/API/File"><i>(reference)</i></a> with various properties that we will use to make a 
		simple view element.
		<br>
		Reviewing the html that is generated by the <strong>fileToUpload</strong> function, there is one thing important to note. The <strong>fileObject</strong> div id attribute is set to the name of the file, 
		minus the period and extension. The id is used to know which view object will be updated during the upload process.
	</p>
	
	<xmp>
		function fileSelected() {

			var files = $("#fileToUpload")[0].files;
		
			//append file info template
			for (var i = 0; i < files.length; i++) {
				$(".files").append(fileToUpload(files[i]));
			}
		}
		
		function fileToUpload(file) {

			if (file)
				var fileSize = 0;
			if (file.size > 1024 * 1024)
				fileSize = (Math.round(file.size * 100 / (1024 * 1024)) / 100).toString() + 'MB';
			else
				fileSize = (Math.round(file.size * 100 / 1024) / 100).toString() + 'KB';
		
			var html = '<div class="fileObject" id="' + file.name.split(".")[0] + '">' + 
				       '    <div>File Name: <span class="fileName">' + file.name + '</span></div>' + 
				       '    <div>File Size: <span class="fileSize">' + fileSize + '</span></div>' + 
				       '    <div>File Type: <span class="fileType">' + file.type + '</span></div>' + 
				       '    <div>Progress: <span class="progressMeter"></span></div>' + 
				       '</div>';
		
			return html;
		
		}
	</xmp>
	
	<p>
		At this point, the files should be staged and ready to upload. Clickong on the Submit button fires the <strong>uploadFile()</strong> function, which is a function that grabs 
		the staged files for processing. The files are handed one-by-one to the singleUpload function.
	</p>
	
	<xmp>
		function uploadFile() {

			var files = $("#fileToUpload")[0].files;
		
			for (var i = 0; i < files.length; i += 1) {
				var file = files[i];
				singleUpload(file);
			}
		}
		
		function singleUpload(file) {

			var name = file.name,
			    xhr = new XMLHttpRequest(),
			    fd = new FormData();
			    
			fd.append("fileToUpload", file);
			
			xhr.addEventListener("load", uploadComplete, false);
		    xhr.addEventListener("error", uploadFailed, false);
		
			xhr.upload.addEventListener("progress", function(e) {
		
				uploadProgress(e, name.split(".")[0]);
			}, false);
		
			xhr.open("POST", "upload.php");
			xhr.send(fd);
		
			fd.append("fileToUpload", file);
		}
	</xmp>
	<p>
		The <strong>singleUpload</strong> function is the most complex, but is nothing too difficult. This uses an XMLHttpRequest object, and a 
		FormData<a href="https://developer.mozilla.org/en-US/docs/Web/API/FormData"><i> (reference)</i></a>. We append our file that we passed in to 
		the new form data object. This is simply a name-value pair.
		<br>
		Next, we add a load, error, and progress callback. Again nothing too difficult. The <strong>uploadProgress</strong> callback uses an anonymous inner function 
		to update progress meter. This is where the file name as an id will come in handy, allowing us to update the correct progress meter for the correct file.
		Remember to remove the extension from the filename.
	</p>
	
	<xmp>
		function uploadProgress(evt, name) {

			if (evt.lengthComputable) {
				var percentComplete = Math.round(evt.loaded * 100 / evt.total);
				$("#" + name + " div span.progressMeter").text(percentComplete.toString() + '%');
			}
		}
		
		//Other callbacks
		function uploadComplete(evt) {
			/* This event is raised when the server send back a response */
			console.log(evt.target.responseText);
		}
		
		function uploadFailed(evt) {
			console.log("There was an error attempting to upload the file.");
		}
		
	</xmp>
	
	<p>
		That's about it. No real need for a plug-in.
	</p>

</article>