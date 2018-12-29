/***
 * 字数统计函数
 * @param element 要监听的元素
 * @param countElement 存放字数结果的元素
 * @param maxCount 最大字数限制
 */
function wordCount(element, countElement, maxCount) {
    $(element).on('change,keyup', function () {
        var inputCount = $(this).val().length;
        if (inputCount >= 0) {
            maxCount && !(inputCount <= maxCount) ? ($(countElement).text(maxCount), $(this).val($(this).val().substring(0, maxCount)))   : $(countElement).text(inputCount);
        } else {
            $(countElement).text(0);
        }
    });
}
