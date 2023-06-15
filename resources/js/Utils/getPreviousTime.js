const getPreviousTime = (relativeDate = new Date(), days = 1) => {
    const previous = new Date(relativeDate.getTime());
    previous.setDate(previous.getDate() - days);
    return previous.getTime();
};

export default getPreviousTime;
