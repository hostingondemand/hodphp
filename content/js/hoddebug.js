hoddebug=new function(serverInput){
    console.log("%c Debugging is enabled. The hoddebug object is available for debug purposes.","color:green;");
    this.getErrors=function(){
        console.log("===Errors===");
        for(errorKey in serverInput.errors){
            var error=serverInput.errors[errorKey];
            console.log("%c "+error.title+":","color:red;");
            console.log("details",error.detail);
            console.log("stack trace",error.stackTrace);
            console.log("----------")
        }
    return null;
    }
}(_hoddebugInitVars);

hoddebug.getErrors();