import PostCard, { type PostCardProps } from "@/components/shared/cards/PostCard";
import SectionHeader from "@/components/home/SectionHeader";

interface PostListSectionProps {
  title: string;
  subtitle?: string;
  posts: PostCardProps[];
  href?: string;
  linkLabel?: string;
  className?: string;
}

export default function PostListSection({
  title,
  subtitle,
  posts,
  href,
  linkLabel,
  className = "",
}: PostListSectionProps) {
  if (!posts || posts.length === 0) return null;

  return (
    <section className={`py-8 md:py-12 ${className}`}>
      <SectionHeader
        title={title}
        subtitle={subtitle}
        href={href}
        linkLabel={linkLabel}
      />

      {/* موبایل: کاروسل افقی / دسکتاپ: گرید 4 ستونه */}
      <div className="flex overflow-x-auto md:grid md:grid-cols-4 gap-3 md:gap-5 snap-x snap-mandatory md:snap-none scrollbar-hide pb-2 md:pb-0">
        {posts.map((post) => (
          <PostCard
            key={post.slug}
            slug={post.slug}
            title={post.title}
            excerpt={post.excerpt}
            image={post.image}
            category={post.category}
            readTime={post.readTime}
            href={post.href}
          />
        ))}
      </div>
    </section>
  );
}