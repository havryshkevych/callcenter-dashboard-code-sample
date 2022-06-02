<?php

namespace App\Doctrine;

use Doctrine\ORM\Query\AST\OrderByClause;
use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Tools\Pagination\RowNumberOverFunction;

/**
 * DQL: SELECT ROW_NUMBER(alias.field) FROM Entity alias
 * SQL: SELECT ROW_NUMBER() OVER(ORDER BY alias.field ASC) FROM Entity alias
 */
final class RowNumberFunction extends RowNumberOverFunction
{
    /**
     * @inheritDoc
     */
    public function parse(Parser $parser): void
    {

        $this->orderByClause = null;

        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);

        $orderByItems = [];
        $orderByItems[] = $parser->OrderByItem();

        while ($parser->getLexer()->isNextToken(Lexer::T_COMMA)) {
            $parser->match(Lexer::T_COMMA);

            $orderByItems[] = $parser->OrderByItem();
        }

        $parser->match(Lexer::T_CLOSE_PARENTHESIS);

        $this->orderByClause = new OrderByClause($orderByItems);
    }
}
