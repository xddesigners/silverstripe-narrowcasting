export const initClock = (md) => {
    if( $('.clock').length==0 ) return;
    timer();
    setInterval(timer,1000);
};

function timer(){

    const clock__hours = $('.clock__hours');
    const clock__minutes = $('.clock__minutes');
    const clock__seconds = $('.clock__seconds');
    const clock__ampm = $('.clock__ampm');

    const now = new Date();

    //Get seconds, minute, an hours from Date.
    const seconds = now.getSeconds(),
        minutes = now.getMinutes(),
        hours = now.getHours();

    //Make hours either 12 or 24 hour format
    let hours_24 = true;
    let hours_12;
    if(hours_24){
        hours_12 = hours; //Make 24 hour format
    } else {
        hours_12 = hours % 12 || 12; //Make hours 12 hour format
    }

    //Make the values always double-digit
    let secondsDbl = ("0" + seconds).slice(-2),
        minutesDbl = ("0" + minutes).slice(-2),
        hoursDbl   = ("0" + hours_12).slice(-2);

    //Seperate the two digits of each into an array
    const secondsOutput = [],
        stringNumberSeconds = secondsDbl.toString();

    const minutesOutput = [],
        stringNumberMinutes = minutesDbl.toString();

    const hoursOutput = [],
        stringNumberHours = hoursDbl.toString();

    if( clock__hours.html()!=stringNumberHours ) {
        clock__hours.html(stringNumberHours);
    }
    if( clock__minutes.html()!=stringNumberMinutes ) {
        clock__minutes.html(stringNumberMinutes);
    }
    clock__seconds.html(stringNumberSeconds);

    //Decide when AM or PM should display.
    if(now.getHours() < 12){
        clock__ampm.html("AM");
    }else{
        clock__ampm.html("PM");
    }

}