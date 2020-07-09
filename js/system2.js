$(function () {
    let player1 = {
        username: "cpu",
        login: "true",
        animationEnd: "true",
        nowStage: 0,
        nowCell: 0,
        level: 1
    };
    let player2 = {
        username: "cpu",
        login: "true",
        animationEnd: "true",
        nowStage: 0,
        nowCell: 0,
        level: 1
    };
    let player3 = {
        username: "cpu",
        login: "true",
        animationEnd: "true",
        nowStage: 0,
        nowCell: 0,
        level: 1
    };
    let player4 = {
        username: "cpu",
        login: "true",
        animationEnd: "true",
        nowStage: 0,
        nowCell: 0,
        level: 1
    };
    let nowplayer;

    if (gameStatus == "newgame") {
        $("#newgame_top").css("display", "flex");
    } else if (gameStatus == "loadgame") {
        $("#loadgame_top").css("display", "flex");
    } else {
        $("#waitgame_top").css("display", "flex");
    }

    $("#game_start").on("click", function () {
        playerArray = [player1.username, player2.username, player3.username, player4.username];
        for (let i = playerArray.length - 1; i >= 0; i--) {
            const rn = Math.floor(Math.random() * (i + 1));
            [playerArray[i], playerArray[rn]] = [playerArray[rn], playerArray[i]];
        }
        $.ajax({
            type: "POST",
            url: "sugoroku_shuffle.php",
            data: {
                "game_id": gameID,
                "player1": playerArray[0],
                "player2": playerArray[1],
                "player3": playerArray[2],
                "player4": playerArray[3]
            }
        }).done(function () {
            games_server.doc(gameID).update({
                player1: {
                    username: playerArray[0],
                    login: "true",
                    animationEnd: "true",
                    nowStage: 0,
                    nowCell: 0,
                    level: 1
                },
                player2: {
                    username: playerArray[1],
                    login: "true",
                    animationEnd: "true",
                    nowStage: 0,
                    nowCell: 0,
                    level: 1
                },
                player3: {
                    username: playerArray[2],
                    login: "true",
                    animationEnd: "true",
                    nowStage: 0,
                    nowCell: 0,
                    level: 1
                },
                player4: {
                    username: playerArray[3],
                    login: "true",
                    animationEnd: "true",
                    nowStage: 0,
                    nowCell: 0,
                    level: 1
                },
                nowPlayer: "player1"
            });
            $("#newgame_top").css("display", "none");
            $("#stage1").css("display", "flex");
        }).fail(function () {
            // 通信失敗時の処理を記述
            console.log("error")
        });

    });

    async function gameExists() {
        if (hostOrGuest == "host") {
            let existOrNot = false;
            await games_server.where("gameID", "==", gameID).get().then(function (querySnapshot) {
                querySnapshot.forEach(async function () {
                    existOrNot = true;
                });
            });
            if (existOrNot == false) {
                let setting = {
                    gameID: gameID,
                    player1: {
                        username: username,
                        login: "true",
                        animationEnd: "true",
                        nowStage: 0,
                        nowCell: 0,
                        level: 1
                    },
                    player2: {
                        username: "cpu",
                        login: "true",
                        animationEnd: "true",
                        nowStage: 0,
                        nowCell: 0,
                        level: 1
                    },
                    player3: {
                        username: "cpu",
                        login: "true",
                        animationEnd: "true",
                        nowStage: 0,
                        nowCell: 0,
                        level: 1
                    },
                    player4: {
                        username: "cpu",
                        login: "true",
                        animationEnd: "true",
                        nowStage: 0,
                        nowCell: 0,
                        level: 1
                    },
                    nowPlayer: ""
                };
                games_server.doc(gameID).set(setting);
            }
        };
    }

    if (hostOrGuest == "guest") {
        games_server.where("gameID", "==", gameID).get().then(async function (querySnapshot) {
            let playerUpdate = false;
            await querySnapshot.forEach(function (doc) {
                if (doc.data()[playerNumber].username == "cpu") {
                    playerUpdate = true;
                };
            });
            if (playerUpdate == true) {
                games_server.doc(gameID).update({
                    [playerNumber]: {
                        username: username,
                        login: "true",
                        animationEnd: "true",
                        nowStage: 0,
                        nowCell: 0,
                        level: 1
                    }
                });
            }
        });
    }

    function playersRewrite() {
        $("#player1").text("player1:" + player1.username);
        $("#player2").text("player2:" + player2.username);
        $("#player3").text("player3:" + player3.username);
        $("#player4").text("player4:" + player4.username);
    };

    function diceTouchable() {
        if (nowplayer == playerNumber) {
            $("#dice").removeClass("untouchable");
        } else {
            $("#dice").addClass("untouchable");
        }
    }

    function playerNumberSearch() {
        if (player1.username == username) {
            playerNumber = "player1"
        } else if (player2.username == username) {
            playerNumber = "player2"
        } else if (player3.username == username) {
            playerNumber = "player3"
        } else if (player4.username == username) {
            playerNumber = "player4"
        };
    }

    $("#dice").on("click", function () {
        const diceRN = Math.floor(Math.random() * 6 + 1);
        $("#info_box").text(diceRN + "が出た。");
        games_server.doc(gameID).update({
            [playerNumber]: {
                username: username,
                login: "true",
                animationEnd: "true",
                nowStage: [playerNumber].nowStage,
                nowCell: [playerNumber].nowCell + diceRN,
                level: [playerNumber].level
            }
        });
    });

    games_server.doc(gameID).onSnapshot(doc => function () {
        console.log("true");

        function a() {
            player1 = doc.data().player1;
            player2 = doc.data().player2;
            player3 = doc.data().player3;
            player4 = doc.data().player4;
            nowplayer = doc.data().nowPlayer;
            console.log("true");
        }
        a();
        playersRewrite();
        diceTouchable();
        playerNumberSearch();
    });



    gameExists();

});