// let minus = document.querySelector(".minus")
// let plus = document.querySelector(".plus")
// let exeption = document.querySelector(".exeption")

// count = '';
// plus.onclick = function () {
//     console.log("test")
//     exeption.innerHTML = count++
// }
// minus.onclick = function () {
//     console.log("test")
//     exeption.innerHTML = count--
// }


let plus = document.querySelector(".plus")
let minus = document.querySelector(".minus")

let NUM = document.querySelector(".exeption")

let countInput = 0;

plus.onclick = function () {
    if (countInput < 10) {
        countInput += 1;
    }
    
    if (countInput <= 10) {
        NUM.innerHTML = " " + countInput
    } else {
        console.log("done")
    }
}

minus.onclick = function () {
    if (countInput > 0) {
        countInput -= 1;
    }
    
    if (countInput <= 10) {
        NUM.innerHTML = " " + countInput
    } else {
        console.log("done")
    }
}

// end input form