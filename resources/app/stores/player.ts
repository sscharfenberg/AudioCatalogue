/******************************************************************************
 * store for audio player
 *****************************************************************************/
import { defineStore } from "pinia";

/**
 * @function store definition
 */
export const usePlayerStore = defineStore("player", {
    state: (): PlayerState => {
        return {
            autoplay: false,
            shuffle: false,
            audiobooks: []
        };
    },
    actions: {
        /**
         * @function set bookmark of audiobook
         * @param AudiobookEncodedName
         * @param TrackEncodedPath
         * @param timestamp
         */
        setAudiobookBookmark(AudiobookEncodedName: string, TrackEncodedPath: string, timestamp: number) {
            console.log(
                "set audiobookBookmark for book",
                AudiobookEncodedName,
                "to track",
                TrackEncodedPath,
                timestamp
            );
            // do we already have a bookmark for this book?
            if (this.audiobooks.find(book => book.audiobookEncodedName === AudiobookEncodedName)) {
                this.audiobooks = this.audiobooks.map(book => {
                    if (book.audiobookEncodedName === AudiobookEncodedName) {
                        book.trackEncodedPath = TrackEncodedPath;
                        book.timestamp = timestamp;
                    }
                    return book;
                });
            } else {
                this.audiobooks.push({
                    audiobookEncodedName: AudiobookEncodedName,
                    trackEncodedPath: TrackEncodedPath,
                    timestamp: 0
                });
            }
        },

        /**
         * @function clear and audiobook bookmark
         * @param AudiobookEncodedName
         */
        clearAudiobookBookmark(AudiobookEncodedName: string) {
            this.audiobooks = this.audiobooks.filter(book => book.audiobookEncodedName !== AudiobookEncodedName);
        }
    },
    getters: {
        getAudiobookBookmark: state => {
            return (AudiobookEncodedName: string) =>
                state.audiobooks.find(book => book.audiobookEncodedName === AudiobookEncodedName);
        }
    },
    persist: {
        key: "player",
        storage: localStorage
    }
});

interface PlayerState {
    autoplay: boolean;
    shuffle: boolean;
    audiobooks: Array<object>;
}
