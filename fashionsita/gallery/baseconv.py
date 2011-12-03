# Copyright (c) 2010 Taurinus Software Ltda. All rights reserved.
# Copyright (c) 2009 Simon Willison. All rights reserved.
# Copyright (c) 2002 Drew Perttula. All rights reserved.
#
# License:
#   Python Software Foundation License version 2
#
# See the file "LICENSE" for terms & conditions for usage, and a DISCLAIMER OF
# ALL WARRANTIES.
#
# This Baseconv distribution contains no GNU General Public Licensed (GPLed)
# code so it may be used in proprietary projects just like prior ``baseconv``
# distributions.
#
# All trademarks referenced herein are property of their respective holders.
#
import uuid

"""
Convert numbers from base 10 integers to base X strings and back again.

Sample usage::

  >>> base20 = BaseConverter('0123456789abcdefghij')
  >>> base20.encode(1234)
  '31e'
  >>> base20.decode('31e')
  '1234'
  >>> base20.encode(-1234)
  '-31e'
  >>> base20.decode('-31e')
  '-1234'
  >>> base11 = BaseConverter('0123456789-', signal='$')
  >>> base11.encode('$1234')
  '$-22'
  >>> base11.decode('$-22')
  '$1234'

"""

class BaseConverterAlphabets(object):		
	def binary(self):
		return '01'
	
	def hex(self):
		return '0123456789ABCDEF'
	
	def base56(self):
		return ('ABCDEFGHJKLMNPQRSTUVWXYZ'
	                           'abcdefghijkmnpqrstuvwxyz'
	                           '23456789')
	def base62(self):
		return ('ABCDEFGHIJKLMNOPQRSTUVWXYZ'
	                           'abcdefghijklmnopqrstuvwxyz'
	                           '0123456789')
	def base64(self):
		return base62() + '-_'

class BaseConverter(object):
    decimal_digits = '0123456789'

    def __init__(self, digits, signal='-'):
        self.signal = signal
        self.digits = digits

    def encode(self, string):
        return self.convert(string, self.decimal_digits, self.digits,
                            self.signal)

    def decode(self, string):
        return self.convert(string, self.digits, self.decimal_digits,
                            self.signal)

    def convert(number, fromdigits, todigits, signal):
        if (str(number)[0] == signal):
            number = str(number)[1:]
            neg = 1
        else:
            neg = 0

        # make an integer out of the number
        x = 0
        for digit in str(number):
           x = x * len(fromdigits) + fromdigits.index(digit)

        # create the result in base 'len(todigits)'
        if x == 0:
            res = todigits[0]
        else:
            res = ''
            while x > 0:
                digit = x % len(todigits)
                res = todigits[digit] + res
                x = int(x / len(todigits))
            if neg:
                res = signal + res
        return res
    convert = staticmethod(convert)

def generate_key():
	#key = str(uuid.uuid1().hex)
	alpha = BaseConverterAlphabets()
	base = BaseConverter(alpha.base56())
	uuid_key = uuid.uuid1()
	encoded_key = base.encode(uuid_key.int)
	return encoded_key, str(uuid_key)

def encode_to_key(i):
	#key = str(uuid.uuid1().hex)
	alpha = BaseConverterAlphabets()
	base = BaseConverter(alpha.base56())
	encoded_key = base.encode(i)
	return encoded_key	
		
"""
bin = BaseConverter(BINARY_ALPHABET)
hexconv = BaseConverter(HEXADECIMAL_ALPHABET)
base56_urlsafe = BaseConverter(BASE56_URLSAFE_ALPHABET)
base62_urlsafe = BaseConverter(BASE62_URLSAFE_ALPHABET)
base64_urlsafe = BaseConverter(BASE64_URLSAFE_ALPHABET, signal='$')

if __name__ == '__main__':
    # doctests
    import doctest
    doctest.testmod()

    # other tests
    nums = [-10 ** 10, 10 ** 10] + range(-100, 100)
    for converter in [bin,hexconv,base56_urlsafe,base62_urlsafe,base64_urlsafe]:
        if converter.signal == '-':
            for i in nums:
                assert i == int(converter.decode(converter.encode(i))), \
                    '%s failed' % i
        else:
            for i in nums:
                i = str(i)
                if i[0] == '-':
                    i = converter.signal + i[1:]
                assert i == converter.decode(converter.encode(i)), \
                    '%s failed' % i
"""