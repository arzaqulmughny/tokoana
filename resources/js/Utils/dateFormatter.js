const dateFormatter = (epochTime, time = true) => {
    const date = new Date(epochTime);
    const result = `${date.getFullYear()}/${date.getMonth()}/${date.getDate()}`;
    if (time === true) {
        return `${result} at ${date.getHours()}:${date.getMinutes()} ${date.getHours() >= 12 ? "PM" : "AM"}`;
    }
    return result;
};

export default dateFormatter;
