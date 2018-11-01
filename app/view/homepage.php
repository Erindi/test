<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <title>Главная</title>
    <link rel="stylesheet" type="text/css" href="/css/bootstrap.min.css" />
</head>
<body>
<div class="row">
    <div class="col-md-5">
        <form action="/" method="POST">
            <div class="form-group">
                <label for="linkInput">Email address</label>
                <input type="text" name="url" class="form-control" id="linkInput" placeholder="Enter url">
            </div>
            <div class="form-group">
                <label for="depthInput">Depth</label>
                <input type="text" name="depth" class="form-control" id="depthInput" placeholder="Enter parse depth">
            </div>
            <div class="form-group">
                <label for="counterInput">Max emails</label>
                <input type="text" name="maxEmails" class="form-control" id="counterInput" placeholder="Enter maximum number of emails">
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>
    <div class="col-md-7">
        <?php if (isset($emailsCount) && isset($url)): ?>
            <table>
                <tr>
                    <td>Website</td>
                    <td class="right">emailsCount</td>
                </tr>
                <tr>
                    <td><a href="/" id="viewEmails" data-id="<?php echo $insertedId ?? ''; ?>"><?php echo $url; ?></a></td>
                    <td class="float-right"><?php echo $emailsCount; ?></td>
                </tr>
            </table>
        <?php endif; ?>
    </div>
</div>
<br>
<div class="row">
    <div class="col-md-12" id="parseResult"></div>
</div>
</body>
<script src="/js/jquery-3.3.1.min.js" type="text/javascript"></script>
<script src="/js/custom.js" type="text/javascript"></script>
</html>
