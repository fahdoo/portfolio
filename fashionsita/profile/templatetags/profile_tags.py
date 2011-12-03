from django import template
from django.template.defaultfilters import stringfilter

def person(user):
    """
    Renders a single user object.
    """
    return {'user': user}


def truncate(input_str, arg):
    """
    Truncates a string of characters to a certain length, eliding as necessary.
    """
    try:
        input_str = unicode(input_str)
        arg = int(arg)
    except ValueError:
        return input_str
    if len(input_str) > arg:
        return input_str[:arg-3] + '...'
    return input_str
truncate = stringfilter(truncate)

register = template.Library()
register.filter('truncate', truncate)
register.inclusion_tag('person.html')(person)
