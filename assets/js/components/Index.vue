
<template>
  <button @click="sendMessage()"></button>
</template>


<script>
export default {
  data() {
    return {
      count: 0,
      webSocket: null,
    };
  },
  methods: {
    sendMessage(){
      this.webSocket.send("Here's some text that the server is urgently awaiting!");
    },
    onOpen(event) {
      console.log("opened", event);
    },
    onMessage(event) {
      console.log(event.data);
    },
  },
  mounted() {

    this.webSocket = new WebSocket("ws://localhost:10000");

    this.webSocket.onopen = this.onOpen;

    this.webSocket.onmessage = this.onMessage;
  },
};
</script>