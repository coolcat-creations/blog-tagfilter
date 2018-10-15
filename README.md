# Joomla Blog Override with tag and custom field filters

This menutype for a blog shows items in an isotope with the possibility to filter by tag. 

It's optimized for Bootstrap 4, however there are options to change the css framework - just report if you have issues with the alternate frameworks.

If your tags are nested they are grouped by their parent tag.

You can enable also filtering by custom field. Then the values coming from the custom fields have to be comma-seperated values. Custom Fields that should not be filtered can be excluded.

To enable the override:
1) put the com_content folder into the html/ folder in your frontend template
2) put the contents of the language_overrides folders into their destination (administrator/languages/overrides and languages/overrides)
3) place the layout folder into the html/ folder in your frontend template

I am doing that for fun and to support the community. However, if you feel like I should have more time for fun things you can support me by "donating" something. I will send you an invoice if needed. https://www.paypal.me/coolcatcreations/

![demo](https://raw.githubusercontent.com/coolcat-creations/blog-tagfilter/master/demo.gif)

Todos:
- Translate some missing strings
- Showing Label instead of Alias for the heading
- Order of the filters (?) ...
- Optimize Classes for CSS frameworks beside BS4
- Bugfixing
