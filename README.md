# Threadz

***
Last Modified Date: Oct 7, 2016  
Name: Threadz  
Author: Matt Lewis  
Contact: mlewis23@ewu.edu  
Organization: Eastern Washington University - Instructional Technology  
Copyright: Creative Commons Attribution-NonCommercial-ShareAlike 4.0 International License Version: 1.0  

***
////////////////////////////////////////////////////
##    TOC
////////////////////////////////////////////////////

[About](#about)  
[Permissions and Security](#permissions-and-security)  
[Licensing](#licensing)  
[Installation Requirements](#installation-requirements)  
[Developer Key Generation](#developer-key-generation)  
[Getting Started - Installation](#getting-started-installation)  
[Getting Started - Stylesheet](#getting-started-stylesheet)  
[Adding Canvas LTI](#adding-canvas-lti)  
[Acknowledgements](#acknowledgements)  


////////////////////////////////////////////////////
###  About ##
////////////////////////////////////////////////////

Built as a Learning Tools Interoperability (LTI) integration to Canvas (the learning management system at Eastern Washington University), Threadz is a discussion visualization tool that adds graphs and statistics to Canvas discussions.
 
Online discussions provide valuable information about the dynamics of a course and its constituents.  Much of this information is found within the content of the posts, but other elements are hidden within the social network connection and interactions between students and between students and instructors.  Threadz is a tool that extracts this hidden information and puts it on display.
 
The visual representations created from social network connections and interactions between students and instructors in a discussion assist in identifying specific behaviors and characteristics within the course, such as: learner isolation, non-integrated groups, instructor-centric discussions, and key integration (power) users and groups. By identifying these behaviors and characteristics, the instructor can affect change in these interactions to help make the discussions and classroom discourse more accessible to all.

The permissions to use this tool are not restricted by role. Both students and teachers are able to access Threadz when made available.  To disable Threadz in Canvas, within the course settings Navigation tab, move Threadz below the navigation line.
 
In it's current state, Threadz does not support other LMS platforms besides Instructure's Canvas. If you are interested in developing Threadz for a different LMS please contact us, we'd enjoy partnering with you.



//////////////////////////////////////////////
###   Permissions and Security  ##
//////////////////////////////////////////////

Threadz uses the IMSBasicLTI to oAuth the LTI tool into Canvas.  Threadz needs to authenticate the user in order to get the user's token. This token is not saved, but used immediately to access the Canvas API to collect the discussion data for the course.  This data is then saved into session.

The role permissions for Threadz are not locked down.  Anyone with access to the course discussions will have access to create the discussion visualizations.



////////////////////////////////////////////////////
##    Licensing ##
////////////////////////////////////////////////////

Threadz by Eastern Washington University - Instructional Technology is licensed under a Creative Commons Attribution-NonCommercial-ShareAlike 4.0 International License.

The software is provided "AS IS", without warranty of any kind, express or implied, including but not limited to the warranties of merchantability, fitness for a particular purpose and noninfringement. In no event shall the authors or copyright holders be liable for any claim, damages or other liability, whether in an action of contract, tort or otherwise, arising from, out of or in connection with the software or the use or other dealings in the software.


//////////////////////////////////////////////
###   Installation Requirements ##
//////////////////////////////////////////////

1. Access to a directory on a PHP webserver with a SSL Certificate  
2. Access to php.ini file, or make sure the 'allow_url_fopen' variable is set to on  
3. Canvas developer key  



//////////////////////////////////////////////
###  Developer Key Generation ##
//////////////////////////////////////////////

**For Canvas:**  
Generate Canvas developer key. For access to the API data, Canvas requires a developer key to be generated for each application.

The current process for the creation of Canvas developer keys is to have your Canvas admin generate them. The Canvas guides have a helpful description about the process ([https://guides.instructure.com/m/4214/l/441833-how-do-i-add-a-developer-key-for-an-account](https://guides.instructure.com/m/4214/l/441833-how-do-i-add-a-developer-key-for-an-account)).
  1. When filling out the Dev Key form field 'Redirect URI', make sure this is identical to the directory path where Threadz is on the webserver.
  2. The 'Icon URL' field will be https://<directory path>/images/threadz_icon.png
  3. Be sure to use the client ID (ID) and not the tool ID added at the time the admin generates the key.  


**For Moodle:** Not yet supported  
**For Sakai:** Not yet supported  
**For Blackboard:** Not yet supported  
**For D2L:** Not yet supported  



//////////////////////////////////////////////
###    Getting Started Installation ##
//////////////////////////////////////////////

1. In the php.ini file on the webserver, set 'allow_url_fopen' to on.  
2. Download treadz.zip.  
3. Save Threadz directory onto the websever.  
4. Generate Canvas developer key if you haven't already done so (see [Developer Key Generation](#developer-key-generation) ).  
5. Edit the preferences.php file. Edit the following variables found between lines 15-40.  
    - $domainThreadz = [your server url]        
    - $domainLMS = [your LMS url]       
    - $lms = [your LMS]  
    - $client_id = [your ID]  
    - $client_secret = [your Key]  
    - OPTIONAL
        $shared_secret = [your Key]  
6. Edit the config-threadz.xml file.  
    - There are three lines that need to be edited, all related to the path to your webserver. Modify lines for the launch_url, domain, and url properties with path to your server. Or, you can use the XML Config Builder tool to generate a new xml file (http://www.edu-apps.org/build_xml.html) if you prefer. More information can be found at https://canvas.instructure.com/doc/api/file.tools_intro.html  



//////////////////////////////////////////////
###    Getting Started Stylesheet ##
//////////////////////////////////////////////

Threadz uses the jQuery UI themes.  You can use the jQuery Themeroller to create your own from [http://jqueryui.com/themeroller/](http://jqueryui.com/themeroller/). Saved into the css directory are the jQuery themes cupertino, overcast, paperGrinder, redmond and overcastBluesky. The default theme, overcastBluesky, can be changed from the meta data in threadz.php.



////////////////////////////////////////////////////
###    Adding Canvas LTI ##
////////////////////////////////////////////////////

Canvas has a user guide about how to set up an LTI : https://guides.instructure.com/m/4214/l/74559-how-do-i-configure-an-external-app-for-an-account-using-a-url  
1. Open a course in Canvas  
2. Go to the 'Apps' tab in course Settings  
3. Click 'View App Configurations'  
4. Click 'Add App'
5. Complete the LTI form  
  1. Configuration Type: select 'By URL'from the drop down list  
  2. Name: enter 'Threadz: Discussion Visualization Tool' or another name that makes sense to you.  
  3. Consumer Key: leave empty  
  4. Shared Secret: threadz  (option to change the shared secret on preferences.php)
  5. Config URL: paste the url link to the config-threadz.xml file (Getting Started step 6).  
6. Click 'Submit'


////////////////////////////////////////////////////
###    Acknowledgements ##
////////////////////////////////////////////////////

[SNAPP (Social Network Adapting Pedagogical Practice)](http://www.snappvis.org/)
Dr. Shane Dawson etal.  
The SNAPP tool is the work of Dr. Shane Dawson etal. and is a similar tool that creates visualizations of the interactions within a discussion forum.  SNAPP however is not compatible with Canvas, thus the need for Threadz.


[IMSBasicLTI] (https://code.google.com/p/basiclti4wordpress/source/browse/trunk/producer/mu-plugins/IMSBasicLTI/ims-blti/?r=2)  
Copyright (c) 2007 Andy Smith  
The code provided by Andy Smith creates a secure Oauth class to access the lti launch data.


[D3.js](http://d3js.org/)  
Copyright (c) 2010-2015, Michael Bostock  
D3.js is a JavaScript library for manipulating documents based on data. D3 helps you bring data to life using HTML, SVG, and CSS. D3's emphasis on web standards gives you the full capabilities of modern browsers without tying yourself to a proprietary framework, combining powerful visualization components and a data-driven approach to DOM manipulation.  




