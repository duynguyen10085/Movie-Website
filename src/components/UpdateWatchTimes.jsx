import React, { useState, useEffect } from "react";
import "../styles/CompletedWatchedList.css";

const apiKey = "81eb1fa9c553b464701b00ef4bd78703e6b90c2829f0408b27b28f990536e663";

const WatchedTime = ({ movieID }) => {
    const [count, setCount] = useState(0);

    useEffect(() => {
        const fetchWatchCount = async () => {
            try {
                const response = await fetch(
                    `https://loki.trentu.ca/~duynguyen/3430/assn/cois-3430-2024su-a2-duynguyen10085/api/completedwatchlist/entries/${movieID}/times-watched`,
                    {
                        headers: {
                            "X-API-KEY": apiKey,
                        }
                    }
                );

                if (response.ok) {
                    const result = await response.json();
                    setCount(result.timesWatched || 0); // Set initial count from API response
                } else {
                    console.error("Error fetching watch count:", response.statusText);
                }
            } catch (error) {
                console.error("Error fetching watch count:", error);
            }
        };

        fetchWatchCount();
    }, [movieID]);

    const increment = async () => {
        try {
            const response = await fetch(
                `https://loki.trentu.ca/~duynguyen/3430/assn/cois-3430-2024su-a2-duynguyen10085/api/completedwatchlist/entries/${movieID}/times-watched`,
                {
                    method: 'PATCH',
                    headers: {
                        "X-API-KEY": apiKey,
                        "Content-Type": "application/json",
                    },
                    body: JSON.stringify({ increment: true }), // Send increment flag
                }
            );

            if (response.ok) {
                setCount(prevCount => prevCount + 1); // Increment count on success
            } else {
                console.error("Error incrementing watch count:", response.statusText);
            }
        } catch (error) {
            console.error("Error incrementing watch count:", error);
        }
    };

    return (
            <p>Times Watched: {count}</p>
    );
};

export default WatchedTime;