This extension allows you to add contact form on any cms page and reach the same page after form submit. 
Because ussually when we add contact form on cms page it redirects to the magento's default contact form page.

So to achieve this just install this extension and do the following:

1. Create your CMS page (Admin > Manage Pages)
2. Paste the following HTML where you would like the contact form to be positioned in your CMS page:

{{block type="core/template" name="contactForm" 
form_action="/contacts/index/post" template="contacts/form.phtml"}}

This extension is tested on magento 1.6 to 1.9 version. But if you still have any problem please contact me.

My email address is : mnjsngh101@gmail.com My Skype Id: manoj.singh232