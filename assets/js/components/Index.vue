
<template>
  <div class="container" style="margin-top: 50px">
    <div class="row">
      <div class="col-md-2">
        <button @click="newGame()" type="button" class="btn btn-success">
          New game
        </button>
      </div>
      <div class="col-md-10">
        <h1>{{ state }}</h1>
      </div>
    </div>

    <div class="row" v-for="(row, rowKey) in board" v-bind:key="row">
      <div
        class="col-sm box"
        v-for="(column, columnKey) in row"
        v-bind:key="column"
        @click="place(rowKey, columnKey)"
      >
        <div v-html="column"></div>
      </div>
    </div>
  </div>
</template>


<script>
export default {
  data() {
    return {
      webSocket: null,
      board: [],
      state: "",
    };
  },
  methods: {
    newGame() {
      this.webSocket.send(JSON.stringify({ action: "new_game" }));
    },
    place(rowKey, columnKey) {
      this.webSocket.send(
        JSON.stringify({ action: "place", rowKey, columnKey })
      );
    },
    onOpen(event) {
      this.onMessage(event);
    },
    onMessage(event) {
      if (!event.data) return;

      const { board, state } = JSON.parse(event.data);

      this.board = board;
      this.state = state;
    },
  },
  mounted() {
    this.webSocket = new WebSocket("ws://localhost:10000");

    this.webSocket.onopen = this.onOpen;

    this.webSocket.onmessage = this.onMessage;
  },
};
</script>