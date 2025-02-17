import Home from "./Home";
import Movie from "./Movie";
import ToWatchList from "./ToWatchList";
import CompletedWatchList from "./CompletedWatchedList";

const routes = ([
    {
      path: "/home/page/:pageNumber",
      element: <Home />
    },
    {
      path: "/",
      element: <Home />
    },
    {
        path: "/movies/:pathid",
        element: <Movie />
    },
    {
        path: "/towatchlist/entries",
        element: <ToWatchList />
    },
    {
      path: "/completedwatchlist/entries",
      element: <CompletedWatchList />
    }
  ]);

export default routes;