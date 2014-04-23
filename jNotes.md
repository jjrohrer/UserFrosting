Things to do before usefule to other people
* factor out ClsJjrPath::Cononical
* in db rollout page, you have 'userland/' hardcoded in there - you can probably make that dynamic
* update the instructions

Most Useful Refactors & updates
* separate the templates from the code(?)
* make all of those functions into static methods
* give pages a default permission
* easily specify roles needed for a page in code
* Add concept of groups & be able to add them programatically

Most Useful Instructions
* How to always secure a page and give default actions, like a redirect to login

Security Updates
* obfuscate emails in db
* obfuscate usernames in db
* Improve session security
* Be able to secure a whole directory vi .htaccess, and to verify .htaccess is there
* Do a ghetto tripwire on a whole directory structure w/ certain dirs excluded - with notification

Audit Updates
* Track who changed what
* Track access