<?php


class AppController
{
    private static string $defaultcmd = DefaultCommand::class;
    private static string $defaultview = "fallback";

    public function getCommand(Request $request): Command
    {
        try {
            $descriptor = $this->getDescriptor($request);
            $cmd = $descriptor->getCommand();
        } catch (AppException $e) {
            $request->addFeedback($e->getMessage());
            return new self::$defaultcmd();
        }

        return $cmd;
    }

    public function getView(Request $request)
    {
        try {
            $descriptor = $this->getDescriptor($request);
            $view = $descriptor->getView($request);
        } catch (AppException $e) {
            return new TemplateViewComponent(self::$defaultcmd);
        }

        return $view;
    }

    private function getDescriptor(Request $request): ComponentDescriptor
    {
        $reg = Registry::instance();
        $commands = $reg->getCommands();
        $path = $request->getPath();
        $descriptor = $commands->get($path);

        if (is_null($descriptor))
            throw new AppException("no descriptor for {$path}", 404);

        return $descriptor;
    }
}