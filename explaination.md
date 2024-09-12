# Design Choices

1. Repository Pattern
   The primary design pattern used is the Repository Pattern. which separates the business logic from data access. This design allows for a cleaner, more maintainable code structure by centralizing all database interactions into a repository class. reducing the coupiling between different layers of the application. This pattern also makes it easier to mock the data layer during unit testing.

# Performance Tuning Techniques

1. Redis Caching

-   Redis is used to cache both individual author records ( getById() ) and related books ( books() ) This essential for reducing the load on the database by storing frequently requested data in memory. Cached results are read much faster than querying the database, leading to faster response times.
-   The logic ensures that once a resource is updated ( update() ), the relevant cache is invalidated using Redis::del(), preventing stale data from being server.

2. Selective Database queries

-   In the books() method, eager loading (with('author)) is used to fetch both books and their related author in a single query. This reduces the number of queries run against the database, minimizing overhead caused by the N+1 problem.
-   In the getById() and books() methods, Redis caching further ensures that the database is queried only when necessary.

3. Conditional Caching

-   In both the getById() and books() methods, the cache is only used if the record is available. If the cache is empty, the database is queried, and the data is then stored in Redis for future requests. This lazy caching ensures that memory is only used for records that are actively being requested.

4. Data Consistency

-   Cache invalidation is carefully handled during update() and delete() operations. Before updating or deleting an author, the corresponding cache entry is deleted, ensuring that future queries will retrieve the fresh data from the database and not serve stale content.

5. Error Handling with Null Checks

-   In the getById() and update() methods, I included checks to ensure that operations don’t proceed on invalid or missing records, avoiding unnecessary queries and potential errors.

## Conclusion

This design provides a clean separation between business logic and data access, leverages Redis caching to improve performance, and uses pagination and eager loading to optimize database interactions. By implementing these strategies, we significantly reduce latency and enhance overall performance, especially in scenarios with a high volume of data requests.
