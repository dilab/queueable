# Queueable: Framework Agnostic Queue System

### For Developer
Some general notes when developing this package
+ Driver deals with Message(raw data, mostly in array format)
+ Queue translates Message to Job
+ Job deals with Message
+ Worker deals with Job & Queue objects
