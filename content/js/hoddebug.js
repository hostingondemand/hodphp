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
    this.getProfiles=function(){
        console.log("===profiles===");
        var profiles=serverInput.profiles;
        profiles.sort(function(b,a){
            return ((a.seconds < b.seconds) ? -1 : ((a.seconds > b.seconds) ? 1 : 0));
        });
        for(profileKey in profiles){
            var profile=profiles[profileKey];
            console.log("%c "+profile.name+":","color:red;");
            console.log("occurances",profile.occurances);
            console.log("seconds",profile.seconds);
            console.log("----------")

        }

    }
}(_hoddebugInitVars);

hoddebug.getErrors();
hoddebug.getProfiles();