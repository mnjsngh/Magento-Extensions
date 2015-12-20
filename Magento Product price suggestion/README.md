This is magento product price suggestion extension.
It allows customers to suggest price for the product and which will be sent to the admin.
It will allow the admin to look at the suggested prices and if the admin decide's to change the product's price on customer's request the admin can also 
send email to those customer's with the new price.

So to achieve this just install this extension and do the following:

1. In admin goto System->Configuration->SUGGEST PRICE->Manage suggest price. Here you can set the Receiver's name, Receiver's E-mail, Subject,
Email template and the Success message.

2. In frontend the price suggestion form can be seen on the product page under the a new tab created Price Suggestion. And if you want to call the form
outside the tabs just add this code $this->getChildHtml('suggestprice_index'); in the product's view.phtml.

This extension is tested on magento 1.6 to 1.9 version. But if you still have any problem please contact me.

My email address is : mnjsngh101@gmail.com My Skype Id: manoj.singh232