<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Defines a console prompt
 */
namespace RDev\Console\Prompts;
use RDev\Console\Responses;

class Prompt
{
    /** @var resource The input stream to look for answers in */
    private $inputStream = null;

    /***
     * @param mixed|null $inputStream The input stream to look for answers in
     */
    public function __construct($inputStream = null)
    {
        if($inputStream === null)
        {
            $inputStream = STDIN;
        }

        $this->setInputStream($inputStream);
    }

    /**
     * Prompts the user to answer a question
     *
     * @param Questions\IQuestion $question The question to ask
     * @param Responses\IResponse $response The response to write output to
     * @return mixed The user's answer to the question
     * @throws \RuntimeException Thrown if we failed to get the user's answer
     */
    public function ask(Questions\IQuestion $question, Responses\IResponse $response)
    {
        $response->write($question->getText());

        if($question instanceof Questions\MultipleChoice)
        {
            /** @var Questions\MultipleChoice $question */
            $response->writeln("");
            $choicesAreAssociative = $question->choicesAreAssociative();

            foreach($question->getChoices() as $key => $choice)
            {
                if(!$choicesAreAssociative)
                {
                    // Make the choice 1-indexed
                    $key += 1;
                }

                $response->writeln(" $key) $choice");
            }

            $response->write($question->getAnswerLineString());
        }

        $answer = fgets($this->inputStream, 4096);

        if($answer === false)
        {
            throw new \RuntimeException("Failed to get answer");
        }

        $answer = trim($answer);

        if(empty($answer))
        {
            return $question->getDefaultResponse();
        }

        return $question->formatAnswer($answer);
    }

    /**
     * Sets the input stream
     *
     * @param resource $inputStream The input stream to look for answers in
     */
    public function setInputStream($inputStream)
    {
        $this->inputStream = $inputStream;
    }
}