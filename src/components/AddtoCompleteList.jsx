import { useState, useEffect } from "react";
import "../styles/Movie.css"; 

export default function AddtoCompleteList({ movieID }) {
    const [isAdded, setIsAdded] = useState(false);
    const apiKey = "81eb1fa9c553b464701b00ef4bd78703e6b90c2829f0408b27b28f990536e663";

    useEffect(() => {
        const checkMovieStatus = async () => {
            try {
                const response = await fetch(
                    `https://loki.trentu.ca/~duynguyen/3430/assn/cois-3430-2024su-a2-duynguyen10085/api/completedwatchlist/entries?movieID=${movieID}`,
                    {
                        method: 'GET',
                        headers: {
                            "X-API-KEY": apiKey,
                        },
                    }
                );

                if (response.ok) {
                    const result = await response.json();
                    console.log("Check movie status result:", result);
                    if(result === false) {
                        setIsAdded(false);
                    } else {
                        setIsAdded(true);
                    }
                    
                } else {
                    console.error("Error checking movie status");
                }
            } catch (error) {
                console.error("Error checking movie status:", error);
            }
        };

        checkMovieStatus();
    }, [movieID]);

    const handleClick = async () => {
        try {
            // Delete from towatchlist
            const deleteResponse = await fetch(
                `https://loki.trentu.ca/~duynguyen/3430/assn/cois-3430-2024su-a2-duynguyen10085/api/towatchlist/entries/${movieID}`,
                {
                    method: 'DELETE',
                    headers: {
                        "X-API-KEY": apiKey,
                    },
                }
            );

            if (!deleteResponse.ok) {
                const deleteError = await deleteResponse.json();
                console.error("Error deleting movie from towatchlist:", deleteError);
                return;
            }

            // Check the status of the movie in completedwatchlist
            const checkResponse = await fetch(
                `https://loki.trentu.ca/~duynguyen/3430/assn/cois-3430-2024su-a2-duynguyen10085/api/completedwatchlist/entries?movieID=${movieID}`,
                {
                    method: 'GET',
                    headers: {
                        "X-API-KEY": apiKey,
                    },
                }
            );

            if (checkResponse.ok) {
                const result = await checkResponse.json();
                console.log("Check movie status result:", result); // Debugging output

                if (isAdded) {
                    // Update timesWatched in completedwatchlist
                    const patchResponse = await fetch(
                        `https://loki.trentu.ca/~duynguyen/3430/assn/cois-3430-2024su-a2-duynguyen10085/api/completedwatchlist/entries/${movieID}/times-watched`,
                        {
                            method: 'PATCH',
                            headers: {
                                "X-API-KEY": apiKey,
                                "Content-Type": "application/json",
                            },
                        }
                    );

                    if (patchResponse.ok) {
                        console.log("Movie watch count updated.");
                        // Optionally update state here if needed
                    } else {
                        const patchError = await patchResponse.json();
                        console.error("Error updating movie watch count:", patchError);
                    }
                } else {
                    // Add to completedwatchlist
                    const addResponse = await fetch(
                        `https://loki.trentu.ca/~duynguyen/3430/assn/cois-3430-2024su-a2-duynguyen10085/api/completedwatchlist/entries?movieID=${movieID}`,
                        {
                            method: 'POST',
                            headers: {
                                "X-API-KEY": apiKey,
                                "Content-Type": "application/json",
                            },
                            body: JSON.stringify({ movieID }),
                        }
                    );

                    if (addResponse.ok) {
                        setIsAdded(true);
                    } else {
                        const addError = await addResponse.json();
                        console.error("Error adding movie to completedwatchlist:", addError);
                    }
                }
            } else {
                console.error("Error checking movie status");
            }
        } catch (error) {
            console.error("Error handling movie click:", error);
        }
    };

    return (
        <div className="CompleteList">
            <button
                type="button"
                onClick={handleClick}
            >
                Watch now <i class="fa-solid fa-arrow-up-right-from-square"></i>
            </button>
            
        </div>
    );
}
