## cg_admin ()
字段|描述
:---|:---
id|
name|
pwd|
## cg_category ()
字段|描述
:---|:---
cid|
pid|上级栏目ID
title|
sort_order|排序，序号越大，越靠前
## cg_comment (评论表)
字段|描述
:---|:---
id|
author_id|作者是管理员，存储管理员ID；若是游客评论，该值为0
parent_id|评论的上级ID，若是对文章的评论，该值为0；否则，为被评论的评论的ID
passage_id|被评论的文章
email|邮箱
ip|作者ip
content|评论内容
create_time|评论发表时间
## cg_content ()
字段|描述
:---|:---
coid|
aid|文章ID
content|文章内容
## cg_friend_link (友情链接)
字段|描述
:---|:---
id|
url|url
siteName|网站名称
isShow|是否显示：0--不显示，1--显示
sort|排序，倒序
## cg_passage ()
字段|描述
:---|:---
aid|
cid|文章所属栏目ID
title|
description|文章摘要
author|默认作者
source|文章来源
create_time|发布时间
sort_order|排序，序号越大，越靠前
is_suggest|是否推荐，0--不推荐，1--推荐
view|阅读数
