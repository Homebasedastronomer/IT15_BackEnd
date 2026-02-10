import { Head, Link } from '@inertiajs/react';

type Category = {
    id: number;
    name: string;
};

type Post = {
    id: number;
    category_id: number;
    title: string;
    description: string;
    created_at: string;
};

type Props = {
    categories: Category[];
    posts: Post[];
    selectedCategoryId: number | null;
};

export default function PostsIndex({
    categories,
    posts,
    selectedCategoryId,
}: Props) {
    const isActiveCategory = (categoryId: number | null) =>
        categoryId === selectedCategoryId;

    const categoryHref = (categoryId: number | null) =>
        categoryId ? `/?category=${categoryId}` : '/';

    return (
        <div className="min-h-screen bg-white text-slate-900">
            <Head title="Posts" />

            <div className="mx-auto flex w-full max-w-6xl gap-8 px-6 py-10">
                <aside className="w-56 shrink-0">
                    <h2 className="mb-3 text-sm font-semibold uppercase tracking-wide text-slate-500">
                        Categories
                    </h2>
                    <div className="flex flex-col gap-2">
                        <Link
                            href={categoryHref(null)}
                            className={`rounded-md px-3 py-2 text-sm transition ${
                                isActiveCategory(null)
                                    ? 'bg-slate-900 text-white'
                                    : 'bg-slate-100 text-slate-700 hover:bg-slate-200'
                            }`}
                        >
                            All Posts
                        </Link>
                        {categories.map((category) => (
                            <Link
                                key={category.id}
                                href={categoryHref(category.id)}
                                className={`rounded-md px-3 py-2 text-sm transition ${
                                    isActiveCategory(category.id)
                                        ? 'bg-slate-900 text-white'
                                        : 'bg-slate-100 text-slate-700 hover:bg-slate-200'
                                }`}
                            >
                                {category.name}
                            </Link>
                        ))}
                    </div>
                </aside>

                <main className="flex-1">
                    <h1 className="mb-6 text-2xl font-semibold">Posts</h1>

                    {posts.length === 0 ? (
                        <div className="rounded-lg border border-slate-200 bg-slate-50 p-6 text-slate-600">
                            No posts found for this category yet.
                        </div>
                    ) : (
                        <div className="grid gap-4 sm:grid-cols-2">
                            {posts.map((post) => (
                                <article
                                    key={post.id}
                                    className="rounded-lg border border-slate-200 bg-white p-4 shadow-sm"
                                >
                                    <h3 className="mb-2 text-lg font-semibold">
                                        {post.title}
                                    </h3>
                                    <p className="text-sm text-slate-600">
                                        {post.description}
                                    </p>
                                </article>
                            ))}
                        </div>
                    )}
                </main>
            </div>
        </div>
    );
}
