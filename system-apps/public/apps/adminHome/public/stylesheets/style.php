    <style type="text/css">  


   
/* VERTICAL MENU */

      /*#navcontainerVert{ left: -50px; position: relative; }*/
      #navlistVert li
      {
        display: inline;
        /* for IE5 and IE6 */
      }

      #navlistVert
      {
        width: 200px;
        /* to display the list horizontaly */
        font-family: sans-serif;
        margin: 0 0 0 3em;
        padding: 0;
        border-top: 1px #FFF solid;
        border-left: 1px #FFF solid;
        border-right: 1px #FFF solid;
      }

      #navlistVert a
      {
        width: 99.99%;
        /* extend the sensible area to the maximum with IE5 */
        display: block;
        background-color: #fff;
        border: 1px #FFF solid;
        border-right: 1px #000 solid;
        text-decoration: none;
        color: #000;
      }

      #navlistVert a:hover {
        background-color: #CFC;
      }

      #navlistVert a.selected {
        background-color: #EEF2F3;
        border: 1px #000 solid;
        border-right: 1px #EEF2F3 solid;
 
      }

      .option{ height: 60px; display:block;}
      .icon{ width: 40px; height: 40px; padding-left: 10px; padding-top: 10px; float:left; border: 0px;}
      .details{ float: left; padding-top: 15px; display: block;}
      .line1{ padding-left: 10px;}
      .line2{ font-size: smaller; padding-left: 10px;}



/* hort menu */

      body
      {
        background-color: #EEF2F3;
      }

      #navlistHort
      {
        background-color: #EEF2F3;
        border-bottom: 1px solid #ccc;
        margin: 0;
        padding-bottom: 19px;
        padding-left: 10px;
      }

      #navlistHort ul, #navlistHort li
      {
        display: inline;
        list-style-type: none;
        margin: 0;
        padding: 0;
      }

      #navlistHort a:link, #navlistHort a:visited
      {
        background: #C5D4D7;
        border: 1px solid #ccc;
        color: #666;
        float: left;
        font-size: small;
        font-weight: normal;
        line-height: 14px;
        margin-right: 8px;
        padding: 2px 10px 2px 10px;
        text-decoration: none;
      }

      #navlistHort a:link.selected, #navlistHort a:visited.selected
      {
        background: #EEF2F3;
        border-bottom: 1px solid #EEF2F3;
        color: #000;
      }

      #navlistHort a:hover { color: #00f; background-color: #CCFFCC; }
      #navlistHort ul a:hover { color: #f00 !important; }

      #selectedContent { background-color: #EEF2F3; }
      #content { valign:top; }

      #output { valign:top; min-height: 400px; }
     

    </style>