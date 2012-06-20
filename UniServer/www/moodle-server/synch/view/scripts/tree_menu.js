/*

         Tips & Tricks

             1: Adjust the "function tmenudata0()" numeric id in the statement below to match the numeric id of
                the id='tmenu0' statement within the menu structure and links section above.  The numbers must
                match for the menu to work, multiple menus may be used on a single page by adding new sections
                with new id's.

             2: To specifically define settings for an individual item or container, apply classes or inline styles
                directly to the UL, LI, and A tags within the HTML which defines your menus structure and links above.

*/




/*-------------------------------------------------
************* Parameter Settings ******************
---------------------------------------------------*/


function tmenudata0()
{


    /*---------------------------------------------
    Image Settinngs (icons and plus minus symbols)
    ---------------------------------------------*/

	this.imagePath = 'images/';
	this.imgage_gap = 3			//The image gap is applied to the left and right of the folder and document icons.
						//In the absence of a folder or document icon the gap is applied between the
						//plus / minus symbols and the text only.


	this.plus_image = this.imagePath+"plus.gif"		//specifies a custom plus image.
	this.minus_image = this.imagePath+"minus.gif"		//specifies a custom minus image.
	this.pm_width_height = "9,9"		//Width & Height  - Note: Both images must be the same dimensions.


	this.folder_image = this.imagePath+"folder.gif"	//Automatically applies to all items which may be expanded.
	this.document_image = this.imagePath+"document.gif"	//Automatically applies to all items which are not expandable.
	this.icon_width_height = "16,14"	//Width & Height  - Note: Both images must be the same dimensions.




    /*---------------------------------------------
    General Settings
    ---------------------------------------------*/


	this.indent = 10;			//The indent distance in pixels for each level of the tree.
	this.use_hand_cursor = true;		//Use a hand mouse cursor for expandable items, or the default arrow.




    /*---------------------------------------------
    Tree Menu Styles
    ---------------------------------------------*/


	this.main_item_styles =           "text-decoration:none;		\
                                           font-weight:normal;			\
                                           font-family:Arial;			\
                                           font-size:12px;			\
                                           color:#333333;			\
                                           padding:2px;				"


        this.sub_item_styles =            "text-decoration:none;		\
                                           font-weight:normal;			\
                                           font-family:Arial;			\
                                           font-size:12px;			\
                                           color:#333333;			"



	/* Styles may be formatted as multi-line (seen above), or on a single line as shown below.
	   The expander_hover_styles apply to menu items which expand to show child menus.*/



	this.main_container_styles = "padding:0px;"
	this.sub_container_styles = "padding-top:7px; padding-bottom:7px;"

	this.main_link_styles = "color:#0066aa; text-decoration:none;"
	this.main_link_hover_styles = "color:#ff0000; text-decoration:underline;"

	this.sub_link_styles = ""
	this.sub_link_hover_styles = ""

	this.main_expander_hover_styles = "text-decoration:underline;";
	this.sub_expander_hover_styles = "";


} 