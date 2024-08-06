function addHolidayProperty(e){
    const holiday = e.currentTarget.dataset.holiday;
    const holidayProperty = e.currentTarget.dataset.holiday;
    document.getElementById('holidayToAdd').innerHTML = holiday;
}
