interface TrackProps {
    encodedPath: string;
    track: number;
    disc: number;
    name: string;
}

/**
 * @function format a property of the nav element
 * @param track
 * @return object
 */
const getNavElement = (track: TrackProps): object => {
    return {
        encodedPath: track.encodedPath,
        track: track.track,
        disc: track.disc,
        name: track.name
    };
};

/**
 * @function get navigation for audio player
 * This functions uses the fact that the tracks array is server sorted by
 * ['disc', 'asc'],
 * ['track', 'asc']
 * this means, we can try to find the next/previous element in the tracks array,
 * even if there are "gaps" in the tracks/discs
 *
 * @param tracks
 * @param currentTrackPath
 * @return object
 */
export const getNavigation = (tracks: Array<TrackProps>, currentTrackPath: string | undefined): object => {
    const nav = {
        prev: {},
        next: {}
    };

    // if there is no current track, only use next.
    if (!currentTrackPath) {
        const nexTrack = tracks[0];
        if (nexTrack) nav.next = getNavElement(nexTrack);
        return nav;
    }

    // there is a current track. find the element in tracks array, and increase/decrease
    // the index for next/prev elements. If no element is found, that means we are at the start/end
    // of the array. this works, since the tracks array is server-sorted by
    // ['disc', 'asc'],
    // ['track', 'asc']
    else {
        const current = tracks.find(track => track.encodedPath === currentTrackPath);
        const nextTrack = tracks[tracks.indexOf(current) + 1]; // next track in server-sorted array
        const prevTrack = tracks[tracks.indexOf(current) - 1]; // previous track in server-sorted array
        if (prevTrack) nav.prev = getNavElement(prevTrack);
        if (nextTrack) nav.next = getNavElement(nextTrack);
        return nav;
    }
};
